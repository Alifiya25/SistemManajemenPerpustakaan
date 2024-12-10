document.getElementById('login-form').addEventListener('submit', function (event) {
    const idPustakawan = document.getElementById('ID_PUSTAKAWAN').value;
    const nama = document.getElementById('NAMA').value;

    // Contoh validasi sederhana
    if (!idPustakawan || !nama) {
        event.preventDefault(); // Mencegah form submit
        document.getElementById('error-message').style.display = 'block';
        document.getElementById('error-message').textContent = 'ID Pustakawan dan Nama harus diisi!';
    }
});
