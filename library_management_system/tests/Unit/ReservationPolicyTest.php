<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use App\Models\Semester;
use App\Policies\ReservationPolicy;
use App\Policies\BookPolicy;
use App\Enums\ReservationStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Illuminate\Support\Collection;

class ReservationPolicyUnitTest extends TestCase
{

    use RefreshDatabase;
    /** @test */
    public function it_fails_if_user_is_suspended()
    {
        $user = Mockery::mock(User::class);
        $book = Mockery::mock(Book::class);

        $user->library_status = 'suspended';
        $user->role = 'student';
        $user->full_name = 'John Doe';

        $result = ReservationPolicy::canReserve($user, $book);

        $this->assertEquals('business_rule_violation', $result['result']);
        $this->assertStringContainsString('suspended', $result['message']);
    }

    /** @test */
    public function it_fails_if_student_has_no_active_semester()
    {
        $user = Mockery::mock(User::class);
        $book = Mockery::mock(Book::class);

        $user->library_status = 'active';
        $user->role = 'student';
        $user->full_name = 'Jane Student';

        // Mock Semester::where()->exists() to return false
        Semester::shouldReceive('where->exists')
            ->once()
            ->andReturn(false);

        $result = ReservationPolicy::canReserve($user, $book);

        $this->assertEquals('business_rule_violation', $result['result']);
        $this->assertStringContainsString('active semester', $result['message']);
    }

    /** @test */
    public function it_fails_if_user_already_has_active_reservation()
    {
        $user = Mockery::mock(User::class);
        $book = Mockery::mock(Book::class);

        $user->library_status = 'active';
        $user->role = 'student';
        $user->full_name = 'Jane Student';

        // Mock reservations() method
        $reservationMock = Mockery::mock();
        $reservationMock->shouldReceive('where->whereIn->first')
            ->once()
            ->andReturn((object) ['id' => 1]);

        $user->shouldReceive('reservations')->andReturn($reservationMock);

        $result = ReservationPolicy::canReserve($user, $book);

        $this->assertEquals('business_rule_violation', $result['result']);
        $this->assertStringContainsString('already has an active reservation', $result['message']);
    }

    /** @test */
    public function it_fails_if_user_reaches_max_pending_reservations()
    {
        $user = Mockery::mock(User::class);
        $book = Mockery::mock(Book::class);

        $user->library_status = 'active';
        $user->role = 'student';
        $user->full_name = 'John Student';

        // Mock reservations()->where()->count() for pending count
        $reservationMock = Mockery::mock();
        $reservationMock->shouldReceive('where->count')
            ->once()
            ->andReturn(3); // Already at max

        $user->shouldReceive('reservations')->andReturn($reservationMock);

        config()->set('settings.reservation.student_max_pending_reservations', 3);

        $result = ReservationPolicy::canReserve($user, $book);

        $this->assertEquals('business_rule_violation', $result['result']);
        $this->assertStringContainsString('maximum number of pending reservations', $result['message']);
    }

    /** @test */
/** @test */
public function it_fails_if_book_queue_is_full()
{
    $user = Mockery::mock(User::class);
    $book = Mockery::mock(Book::class);

    $user->library_status = 'active';
    $user->role = 'student';
    $user->full_name = 'John Doe';

    // Mock user reservations for pending count < max
    $userReservations = Mockery::mock();
    $userReservations->shouldReceive('where->count')->andReturn(0);
    $user->shouldReceive('reservations')->andReturn($userReservations);

    // Mock book reservations for current queue length
    $bookReservations = Mockery::mock();
    $bookReservations->shouldReceive('where->count')->andReturn(5);
    $book->shouldReceive('reservations')->andReturn($bookReservations);

    // Config max queue length
    config()->set('settings.reservation.queue_max_length', 5);

    // Mock static BookPolicy::canBeReserved
    Mockery::mock('alias:App\Policies\BookPolicy')
        ->shouldReceive('canBeReserved')
        ->andReturn(['result' => 'success']);

    $result = ReservationPolicy::canReserve($user, $book);

    $this->assertEquals('business_rule_violation', $result['result']);
    $this->assertStringContainsString('maximum length', $result['message']);
}

    /** @test */
    /** @test */
public function it_passes_if_all_conditions_are_met()
{
    $user = Mockery::mock(User::class);
    $book = Mockery::mock(Book::class);

    $user->library_status = 'active';
    $user->role = 'student';
    $user->full_name = 'Jane Doe';

    // Mock user pending reservations < max
    $userReservations = Mockery::mock();
    $userReservations->shouldReceive('where->whereIn->first')->andReturn(null);
    $userReservations->shouldReceive('where->count')->andReturn(1);
    $user->shouldReceive('reservations')->andReturn($userReservations);

    // Mock book queue length < max
    $bookReservations = Mockery::mock();
    $bookReservations->shouldReceive('where->count')->andReturn(1);
    $book->shouldReceive('reservations')->andReturn($bookReservations);

    // Mock Semester active
    Semester::shouldReceive('where->exists')->andReturn(true);

    // Mock static BookPolicy::canBeReserved
    Mockery::mock('alias:App\Policies\BookPolicy')
        ->shouldReceive('canBeReserved')
        ->andReturn(['result' => 'success']);

    // Config limits
    config()->set('settings.reservation.student_max_pending_reservations', 3);
    config()->set('settings.reservation.queue_max_length', 5);

    $result = ReservationPolicy::canReserve($user, $book);

    $this->assertEquals('success', $result['result']);
    $this->assertEquals('Jane Doe', $result['reserver_fullname']);
}
}
