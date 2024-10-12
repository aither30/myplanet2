<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
      integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <link rel="stylesheet" href="styleslogin.css" />
    <title>My PlanET - Login</title>
  </head>
  <body>
    <div class="main-container">
      <!-- Header Hero -->
      <header class="hero">
        <div class="logo">
          <img src="./assets/attribute myplanet/Logo My PlanEt.png" alt="Logo My PlanET" />
          <p>Selamat Datang di <br /><strong>My PlanET</strong></p>
        </div>
        <p class="slogan">Seamless Events, Seamless Solutions</p>
        <div class="auth-options" id="authOptions">
          <button class="btn" onclick="switchForm('login')">MASUK</button>
          <button class="btn" onclick="switchForm('register')">DAFTAR</button>
        </div>
      </header>

      <!-- Form Section -->
      <div class="form-section">
        <!-- Form Lupa Password -->
        <section id="formForgotPassword" class="form-container">
          <form action="./auth/lupa_password.php" method="post" class="form">
            <h2>Lupa Password</h2>
            <div class="input-group">
              <label for="email_reset">Masukkan Email Anda:</label>
              <input type="email" id="email_reset" name="email_reset" placeholder="Masukkan email" required />
            </div>
            <div class="form-actions">
              <button type="submit" class="btn">Reset Password</button>
              <button type="button" class="btn-link" onclick="switchForm('login')">Kembali ke Masuk</button>
            </div>
          </form>
        </section>

        <!-- Form Login -->
        <section id="formLogin" class="form-container">
          <form action="./auth/login.php" method="post" class="form">
            <h2>Masuk</h2>
            <div class="input-group">
              <label for="username">Username:</label>
              <input type="text" id="username" name="username" placeholder="Username" required />
            </div>
            <div class="input-group">
              <label for="password">Password:</label>
              <input type="password" id="password" name="password" placeholder="Password" required />
            </div>
            <div class="form-actions">
              <button type="submit" class="btn">Log In</button>
              <button type="button" class="btn-link" onclick="switchForm('forgotPassword')">Lupa Password?</button>
              <button type="button" class="btn-link" onclick="switchForm('register')">Daftarkan Akun Baru</button>
            </div>
          </form>
        </section>

        <!-- Form Registrasi -->
        <section id="formRegister" class="form-container hidden">
          <form action="./auth/registrasi.php" method="POST" class="form">
            <h2>Daftar</h2>
            <div class="input-group">
              <label for="username_register">Username:</label>
              <input type="text" id="username_register" name="username" placeholder="Username" required />
            </div>
            <div class="input-group">
              <label for="email_register">Email:</label>
              <input type="email" id="email_register" name="email" placeholder="Email" required />
            </div>
            <div class="input-group">
              <label for="password_register">Password:</label>
              <input type="password" id="password_register" name="password" placeholder="Password" required />
            </div>
            <div class="input-group">
              <label for="password_confirmation">Masukkan Ulang Password:</label>
              <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Masukan Ulang Password" required />
            </div>
            <div class="input-group">
              <label for="type_account">Daftar Sebagai:</label>
              <select name="type_account" id="type_account" required>
                <option value="" disabled selected>Pilih tipe akun</option>
                <option value="user">User</option>
                <option value="vendor">Vendor</option>
              </select>
            </div>
            <div class="form-actions">
              <button type="submit" class="btn">Daftar</button>
              <button type="button" class="btn-link" onclick="switchForm('login')">Sudah punya akun? Masuk</button>
            </div>
          </form>
        </section>
      </div>
    </div>

    <script>
      // Array forms menyimpan semua form untuk navigasi mudah
      const forms = {
        login: document.getElementById("formLogin"),
        register: document.getElementById("formRegister"),
        forgotPassword: document.getElementById("formForgotPassword")
      };

      // Fungsi untuk berpindah form
      function switchForm(formName) {
        Object.values(forms).forEach(form => {
          form.classList.add("hidden");
        });
        forms[formName].classList.remove("hidden");
      }

      // Menyimpan form aktif ke localStorage
      document.addEventListener("DOMContentLoaded", () => {
        const activeForm = localStorage.getItem("activeForm") || "login";
        switchForm(activeForm);
      });

      // Fungsi untuk menyimpan form yang diakses
      function setActiveForm(formName) {
        localStorage.setItem("activeForm", formName);
        switchForm(formName);
      }
    </script>
  </body>
</html>
