export function handleViewIssueDetails(copyId, allCopiesData) {
    const copy = allCopiesData.find(c => String(c.id) === String(copyId));

    openIssueDetailsModal(copy.pending_issue_report);
}



export function openIssueDetailsModal(issueReport, copy) {
    console.log('openIssueDetailsModal called with report:', issueReport);
    const modal = document.getElementById('issue-details-modal');
    console.log('openIssueDetailsModal called' + modal);
    if (!modal) return;
    console.log('open modal');

    // Populate modal with issue report data
    const reportType = modal.querySelector('#report-type');
    const reportStatus = modal.querySelector('#report-status');
    const copyNumber = modal.querySelector('#report-copy-number');
    const reportedDate = modal.querySelector('#reported-date');
    const description = modal.querySelector('#report-description');
    const reportedBy = modal.querySelector('#reported-by');
    const borrower = modal.querySelector('#borrower-name');

    if (reportType) {
        const typeLabel = issueReport.report_type === 'damage' ? 'Damaged' : 'Lost';
        const typeClass = issueReport.report_type === 'damage' ? 'bg-orange-100 text-orange-700' : 'bg-red-100 text-red-700';
        reportType.innerHTML = `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ${typeClass}">${typeLabel}</span>`;
    }

    if (reportStatus) {
        const statusLabel = issueReport.status.charAt(0).toUpperCase() + issueReport.status.slice(1);
        const statusClass = issueReport.status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-700';
        reportStatus.innerHTML = `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ${statusClass}">${statusLabel}</span>`;
    }

    if (copyNumber) copyNumber.textContent = `#${copy.copy_number}`;
    if (reportedDate) reportedDate.textContent = new Date(issueReport.created_at).toLocaleString();
    if (description) description.textContent = issueReport.description || 'No description provided';
    if (reportedBy) reportedBy.textContent = `${issueReport.reported_by.role === 'staff' ? '(Staff)' : '(Borrower)'}: ${issueReport.reported_by.fullname || 'N/A'}`;
    if (borrower) borrower.textContent = `${issueReport.borrower.fullname || 'N/A' }`;

    // Show modal with animation
    modal.classList.remove('hidden');
    const modalContent = modal.querySelector('#issue-details-content');
    requestAnimationFrame(() => {
        modal.classList.remove('bg-opacity-0');
        modal.classList.add('bg-opacity-50');
        if (modalContent) {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }
    });
}

/**
 * Close issue details modal
 */
export function closeIssueDetailsModal() {
    const modal = document.getElementById('issue-details-modal');
    if (!modal) return;

    const modalContent = modal.querySelector('#issue-details-content');
    
    modal.classList.remove('bg-opacity-50');
    modal.classList.add('bg-opacity-0');
    if (modalContent) {
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
    }

    setTimeout(() => {
        modal.classList.add('hidden');
    }, 150);
}
