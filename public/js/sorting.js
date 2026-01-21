document.addEventListener('DOMContentLoaded', () => {
    const getCellValue = (tr, idx) => {
        const cell = tr.children[idx];
        return cell.innerText || cell.textContent;
    };

    const comparer = (idx, asc) => (a, b) => ((v1, v2) => 
        v1 !== '' && v2 !== '' && !isNaN(v1) && !isNaN(v2) ? v1 - v2 : v1.toString().localeCompare(v2)
    )(getCellValue(asc ? a : b, idx), getCellValue(asc ? b : a, idx));

    document.querySelectorAll('.table-custom th').forEach(th => {
        // Add sort icon placeholder if not present
        if (!th.querySelector('.sort-icon')) {
            const icon = document.createElement('span');
            icon.className = 'sort-icon';
            th.appendChild(icon);
        }

        th.addEventListener('click', (() => {
            const table = th.closest('table');
            const tbody = table.querySelector('tbody');
            const index = Array.from(th.parentNode.children).indexOf(th);
            const ascending = th.classList.contains('sort-asc');
            
            // Reset others
            table.querySelectorAll('th').forEach(h => h.classList.remove('sort-asc', 'sort-desc'));
            
            // Sort rows
            Array.from(tbody.querySelectorAll('tr'))
                .sort(comparer(index, !ascending))
                .forEach(tr => tbody.appendChild(tr));
            
            // Toggle classes
            th.classList.toggle('sort-asc', !ascending);
            th.classList.toggle('sort-desc', ascending);
        }));
    });
});
