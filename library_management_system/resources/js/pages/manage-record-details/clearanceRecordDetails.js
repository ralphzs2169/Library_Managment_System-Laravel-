import { openDetailsModal, populateRoleBadge } from "../librarian/utils/popupDetailsModal.js";
import { showError } from "../../utils/alerts.js";
import { formatDate } from "../../utils.js";
import { approveClearance, rejectClearance } from "../../ajax/clearanceHandler.js";
import { getClearanceStatusBadge } from "../../utils/statusBadge.js";

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

    // --- Clearance Info ---
    const clearanceStatusBadger = document.getElementById('clearance-record-status-badge');
    clearanceStatusBadger.innerHTML = getClearanceStatusBadge(record.status);
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


initClearanceRecordDetailListeners();
