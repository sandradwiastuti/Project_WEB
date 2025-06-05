// assets/js/scripts.js

document.addEventListener('DOMContentLoaded', function() {
  // ================================
  //  Validasi Form Register (Password vs Konfirmasi)
  // ================================
  const registerForm = document.getElementById('registerForm');
  if (registerForm) {
    registerForm.addEventListener('submit', function(e) {
      const pw = document.getElementById('password');
      const conf = document.getElementById('confirm_password');
      if (pw.value !== conf.value) {
        e.preventDefault();
        alert('Password dan Konfirmasi Password tidak cocok.');
        pw.focus();
      }
    });
  }

  // ================================
  //  Validasi Form Tambah/Edit User (Password vs Konfirmasi)
  // ================================
  const userForm = document.getElementById('userForm');
  if (userForm) {
    userForm.addEventListener('submit', function(e) {
      const pw = document.getElementById('password');
      const conf = document.getElementById('confirm_password');
      if (pw.value !== conf.value) {
        e.preventDefault();
        alert('Password dan Konfirmasi Password tidak cocok.');
        pw.focus();
      }
    });
  }

  // ================================
  //  Live Search di Daftar Transaksi (cari deskripsi)
  // ================================
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

  // ================================
  //  Validasi Form‐form Lain dengan Bootstrap 5 (HTML5 validation + kelas .was-validated)
  //  (Contoh untuk semua form yang ber-id ending “Form”)
  // ================================
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
