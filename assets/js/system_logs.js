document.addEventListener('DOMContentLoaded', function() {
    const logsTable = new DataTable('#logsTable', {
        order: [[0, 'desc']],
        pageLength: 25,
        columns: [
            { width: '15%' },
            { width: '10%' },
            { width: '10%' },
            { width: '15%' },
            { width: '40%' },
            { width: '10%' }
        ]
    });
});
