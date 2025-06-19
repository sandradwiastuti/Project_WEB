// assets/js/scripts.js

document.addEventListener('DOMContentLoaded', function() {
  // Validasi matching password & konfirmasi
  function validatePasswordMatch(formId) {
    const form = document.getElementById(formId);
    if (!form) return;
    form.addEventListener('submit', function(e) {
      const pw1 = form.querySelector('input[name="password"]');
      const pw2 = form.querySelector('input[name="confirm_password"]');
      if (pw1 && pw2 && pw1.value !== pw2.value) {
        e.preventDefault();
        alert('Password dan Konfirmasi Password tidak cocok.');
        pw1.focus();
      }
    });
  }
  validatePasswordMatch('registerForm');
  validatePasswordMatch('userForm');
  validatePasswordMatch('changePasswordForm');

  // Live search transaksi berdasarkan deskripsi
  const searchInput = document.getElementById('search_deskripsi');
  if (searchInput) {
    const tableRows = document.querySelectorAll('#transactionsTable tbody tr');
    searchInput.addEventListener('input', function() {
      const query = this.value.trim().toLowerCase();
      tableRows.forEach(row => {
        const descCell = row.querySelector('.td-deskripsi');
        if (!descCell) return;
        const text = descCell.textContent.trim().toLowerCase();
        row.style.display = text.includes(query) ? '' : 'none';
      });
    });
  }

  // Bootstrap 5 HTML5 validation
  const forms = document.querySelectorAll('form[novalidate]');
  Array.prototype.slice.call(forms).forEach(function(form) {
    form.addEventListener('submit', function(event) {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false);
  });
});
