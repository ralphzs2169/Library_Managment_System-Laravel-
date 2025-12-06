import { openDetailsModal, populateRoleBadge } from "../librarian/utils/popupDetailsModal.js";
import { showError } from "../../utils/alerts.js";
import { formatDate } from "../../utils.js";
import { approveClearance, rejectClearance } from "../../ajax/clearanceHandler.js";

export function initClearanceRecordDetailListeners() {
    const detailButtons = document.querySelectorAll('.open-clearance-record-details');

    detailButtons.forEach(button => {
        button.addEventListener('click', async (e) => {
            e.preventDefault();
            const clearanceRecord = JSON.parse(button.getAttribute('data-clearance-record'));

            if (!clearanceRecord) {
                showError('Data Error', 'Clearance record data is missing or invalid.');
                return;
            }

            const modal = document.getElementById('clearance-record-details-modal');
            const modalContent = document.getElementById('clearance-record-content');
            
            // Scroll to top
            const scrollableContainer = modalContent.querySelector('.px-6.py-5.overflow-y-auto');
            if (scrollableContainer) {
                scrollableContainer.scrollTop = 0;
            }

            await openDetailsModal(modal, modalContent, { clearanceRecord }, initializeClearanceRecordDetailsModal);
        });
    });
}

let clearanceRecordModalListenersInitialized = false;

function initializeClearanceRecordDetailsModal(modal, data) {
    const record = data.clearanceRecord;
    const borrower = record.user;

    // --- Header ---
    document.getElementById('clearance-transaction-id').textContent = `#${record.id}`;

    // --- Borrower Info ---
    const borrowerInitials = document.getElementById('clearance-borrower-initials');
    const borrowerName = document.getElementById('clearance-borrower-name');
    const borrowerIdNumber = document.getElementById('clearance-borrower-id-number');

    borrowerInitials.textContent = (borrower.firstname?.[0] ?? '') + (borrower.lastname?.[0] ?? '');
    borrowerName.textContent = borrower.fullname ?? 'N/A';
    
    let idNumber = 'N/A';
    if (borrower.role === 'student' && borrower.students) idNumber = borrower.students.student_number;
    if (borrower.role === 'teacher' && borrower.teachers) idNumber = borrower.teachers.employee_number;
    borrowerIdNumber.textContent = `User ID: ${idNumber}`;

    populateRoleBadge(modal, '#clearance-borrower-role-badge', borrower);

    // --- Clearance Info ---
    populateStatusBadge(record.status);

    document.getElementById('clearance-record-semester').textContent = record.semester ? record.semester.name : 'N/A';
    
    const requestedBy = record.requested_by;
    document.getElementById('clearance-record-requested-by').textContent = requestedBy ? requestedBy.fullname : 'Self-Requested';
    
    document.getElementById('clearance-record-remarks').textContent = record.remarks || '--';

    // --- Timeline ---
    document.getElementById('clearance-record-requested-at').textContent = formatDate(record.created_at);
    
    const processedAt = document.getElementById('clearance-record-processed-at');
    if (record.status === 'pending') {
        processedAt.textContent = '-- (Pending)';
    } else {
        processedAt.textContent = formatDate(record.updated_at);
    }

    const processedBy = document.getElementById('clearance-record-processed-by');
    if (record.approved_by) {
        processedBy.textContent = record.approved_by.fullname;
    } else {
        processedBy.textContent = '--';
    }

    // --- Actions ---
    const approveBtn = document.getElementById('approve-clearance-btn');
    const rejectBtn = document.getElementById('reject-clearance-btn');

    // Reset buttons (remove old listeners by cloning)
    const newApproveBtn = approveBtn.cloneNode(true);
    approveBtn.parentNode.replaceChild(newApproveBtn, approveBtn);
    
    const newRejectBtn = rejectBtn.cloneNode(true);
    rejectBtn.parentNode.replaceChild(newRejectBtn, rejectBtn);

    if (record.status === 'pending') {
        newApproveBtn.classList.remove('hidden');
        newRejectBtn.classList.remove('hidden');

        newApproveBtn.addEventListener('click', async () => {
             const success = await approveClearance(record.id);
             if (success) closeClearanceRecordModal();
        });

        newRejectBtn.addEventListener('click', async () => {
            const success = await rejectClearance(record.id);
            if (success) closeClearanceRecordModal();
        });

    } else {
        newApproveBtn.classList.add('hidden');
        newRejectBtn.classList.add('hidden');
    }

    // --- Listeners ---
    if (!clearanceRecordModalListenersInitialized) {
        clearanceRecordModalListenersInitialized = true;

        const closeBtn = document.getElementById('close-clearance-record-modal');
        if (closeBtn) closeBtn.onclick = () => closeClearanceRecordModal();

        const closeFooterBtn = document.getElementById('close-clearance-details-button');
        if (closeFooterBtn) closeFooterBtn.onclick = () => closeClearanceRecordModal();

        document.addEventListener('click', function(event) {
            const modalEl = document.getElementById('clearance-record-details-modal');
            if (event.target === modalEl) {
                closeClearanceRecordModal();
            }
        });
    }
}

function closeClearanceRecordModal(withAnimation = true) {
    const modal = document.getElementById('clearance-record-details-modal');
    const modalContent = document.getElementById('clearance-record-content');

    modal.classList.remove('bg-opacity-50');
    modal.classList.add('bg-opacity-0');
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');

    if (withAnimation) {
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 50);
    } else {
        modal.classList.add('hidden');
    }
}

function populateStatusBadge(status) {
    const badgeContainer = document.getElementById('clearance-record-status-badge');
    badgeContainer.innerHTML = '';
    
    const span = document.createElement('span');
    span.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-bold shadow-sm';

    if (status === 'approved') {
        span.classList.add('bg-green-200', 'text-green-700', 'border', 'border-green-100');
        span.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg> Approved`;
    } else if (status === 'rejected') {
        span.classList.add('bg-red-200', 'text-red-700', 'border', 'border-red-100');
        span.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg> Rejected`;
    } else {
        span.classList.add('bg-yellow-100', 'text-yellow-700', 'border', 'border-yellow-200');
        span.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg> Pending`;
    }
    badgeContainer.appendChild(span);
}

initClearanceRecordDetailListeners();
