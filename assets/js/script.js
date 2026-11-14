/**
 * Farm Management System - Client Side JavaScript
 * Form Validation, Confirmation Modals & Dynamic UI Enhancements
 */

document.addEventListener('DOMContentLoaded', function () {

    // 1. Delete Confirmation Prompt
    const deleteButtons = document.querySelectorAll('.btn-delete-confirm');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            const item = this.getAttribute('data-item') || 'this record';
            if (!confirm(`Are you sure you want to delete ${item}? This action cannot be undone.`)) {
                e.preventDefault();
            }
        });
    });

    // 2. Client-Side Table Search Filter
    const searchInput = document.getElementById('tableSearchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            const filter = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('.table-searchable tbody tr');

            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // 3. Form Validation Routines
    const farmForms = document.querySelectorAll('.needs-validation');
    farmForms.forEach(form => {
        form.addEventListener('submit', function (event) {
            let isValid = true;

            // Phone number check (10 digits)
            const phoneInput = form.querySelector('input[name="phone"]');
            if (phoneInput && phoneInput.value.trim() !== '') {
                const phonePattern = /^[0-9]{10}$/;
                if (!phonePattern.test(phoneInput.value.trim())) {
                    alert('Please enter a valid 10-digit phone number (digits only).');
                    phoneInput.focus();
                    event.preventDefault();
                    event.stopPropagation();
                    return false;
                }
            }

            // Date validation (Harvest Date must be >= Planting Date)
            const plantingDate = form.querySelector('input[name="planting_date"]');
            const harvestDate = form.querySelector('input[name="harvest_date"]');
            if (plantingDate && harvestDate && plantingDate.value && harvestDate.value) {
                if (new Date(harvestDate.value) < new Date(plantingDate.value)) {
                    alert('Harvest Date cannot be earlier than Planting Date.');
                    harvestDate.focus();
                    event.preventDefault();
                    event.stopPropagation();
                    return false;
                }
            }

            // Non-negative number inputs
            const numInputs = form.querySelectorAll('input[type="number"]');
            numInputs.forEach(input => {
                if (input.value !== '' && parseFloat(input.value) < 0) {
                    alert(`${input.previousElementSibling ? input.previousElementSibling.innerText : 'Value'} cannot be negative.`);
                    input.focus();
                    isValid = false;
                }
            });

            if (!isValid) {
                event.preventDefault();
                event.stopPropagation();
                return false;
            }

            form.classList.add('was-validated');
        }, false);
    });

    // 4. Auto-calculate Labour Total Wage in forms
    const workingDaysInput = document.getElementById('working_days');
    const dailyWageInput = document.getElementById('daily_wage');
    const totalWageCalc = document.getElementById('total_wage_calc');

    function calculateLabourWage() {
        if (workingDaysInput && dailyWageInput && totalWageCalc) {
            const days = parseFloat(workingDaysInput.value) || 0;
            const wage = parseFloat(dailyWageInput.value) || 0;
            totalWageCalc.value = '₹' + (days * wage).toFixed(2);
        }
    }

    if (workingDaysInput && dailyWageInput) {
        workingDaysInput.addEventListener('input', calculateLabourWage);
        dailyWageInput.addEventListener('input', calculateLabourWage);
    }

    // 5. Auto-calculate Sales Total Income in forms
    const qtyKgInput = document.getElementById('quantity_kg');
    const pricePerKgInput = document.getElementById('price_per_kg');
    const totalSalesCalc = document.getElementById('total_sales_calc');

    function calculateSalesTotal() {
        if (qtyKgInput && pricePerKgInput && totalSalesCalc) {
            const qty = parseFloat(qtyKgInput.value) || 0;
            const price = parseFloat(pricePerKgInput.value) || 0;
            totalSalesCalc.value = '₹' + (qty * price).toFixed(2);
        }
    }

    if (qtyKgInput && pricePerKgInput) {
        qtyKgInput.addEventListener('input', calculateSalesTotal);
        pricePerKgInput.addEventListener('input', calculateSalesTotal);
    }
});
