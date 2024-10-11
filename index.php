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
    <link rel="stylesheet" href="stylessssss.css" />
    <title>My PlanET</title>
  </head>
  <body>
    <div class="container" id="container">
      <div class="hero">
        <div class="head_hero">
          <img src="./assets/attribute/Logo My PlanEt.png" alt="" />
          <p>Selamat Datang di <br /><strong>My PlanET</strong></p>
        </div>
        <div class="slogan">
          <p>Seamless Events, Seamless Solutions</p>
        </div>
        <div class="opsi_login-regs" id="opsiLogin">
          <button onclick="showForm('formMasuk')">MASUK</button>
          <button onclick="showForm('formDaftar')">DAFTAR</button>
        </div>
      </div>
      <form id="formMasuk" class="form_user" action="./auth/login.php" method="post">
        <h2>MASUK</h2>
        <div class="input_account" id="inputAccount">
          <label for="username">Username:</label>
          <input
            type="text"
            id="username"
            name="username"
            placeholder="username"
            required
          />
          <label for="password">Password:</label>
          <input
            type="password"
            id="password"
            name="password"
            placeholder="password"
            required
          />
          <input type="submit" id="submit" name="submit" value="LOG IN" />
        </div>
        <div class="regs_forget-password">
          <div class="regs">
            <a href="#" onclick="toggleDaftar()">Daftarkan Akun Baru</a>
          </div>
          <div class="forget_password">
            <a href="#">Lupa Password?</a>
          </div>
        </div>
      </form>
      <form
    id="formDaftar"
    class="form_regs_user"
    action="./auth/registrasi.php"
    method="POST"
>
    <h2>DAFTAR</h2>
    <div class="inputdata">
        <label for="username_vendor">Username:</label>
        <input
            type="text"
            id="username_vendor"
            name="username"
            placeholder="username"
            required
        />
        <label for="email_vendor">Email:</label>
        <input
            type="email"
            id="email_vendor"
            name="email"
            placeholder="email"
            required
        />
        <label for="password">Password:</label>
        <input
            type="password"
            id="password"
            name="password"
            placeholder="password"
            required
        />
        <label for="password_confirmation">Masukan Ulang Password</label>
        <input type="password" name="password_confirmation" id="password_confirmation" required>

        <label for="type_account">Daftar Sebagai:</label>
        <select name="type_account" id="type_account" required>
            <option value="" disabled selected>Pilih tipe akun</option>
            <option value="user">User</option>
            <option value="vendor">Vendor</option>
        </select>

        <input type="submit" id="submit" name="submit" value="DAFTAR" />
        
        <div class="regs_forget-password">
            <div class="regs">
                <a href="#" onclick="toggleMasuk()">sudah memiliki akun baru</a>
            </div>
            <div class="forget_password">
                <a href="#">Lupa Password?</a>
            </div>
        </div>
    </div>
</form>
    </div>
    <script>



      function showForm(formId) {
        const loginForm = document.getElementById("formMasuk");
        const registerForm = document.getElementById("formDaftar");
        const opsiLogin = document.getElementById("opsiLogin");

        if (formId === "formMasuk") {
          loginForm.style.display = "flex";
          registerForm.style.display = "none";
          opsiLogin.style.display = "none";
          localStorage.setItem("activeForm", "formMasuk"); // Save state
        } else if (formId === "formDaftar") {
          loginForm.style.display = "none";
          registerForm.style.display = "flex";
          opsiLogin.style.display = "none";
          localStorage.setItem("activeForm", "formDaftar"); // Save state
        }
      }

      document.addEventListener("DOMContentLoaded", function () {
        const activeForm = localStorage.getItem("activeForm");

        if (activeForm === "formMasuk") {
          showForm("formMasuk");
        } else if (activeForm === "formDaftar") {
          showForm("formDaftar");
        } else {
          document.getElementById("formMasuk").style.display = "none";
          document.getElementById("formDaftar").style.display = "none";
        }
      });

      function toggleDaftar() {
        showForm("formDaftar");
      }

      function toggleMasuk() {
        showForm("formMasuk");
      }

      function loginGoogle() {
        alert("Login with Google");
      }
      function loginFacebook() {
        alert("Login with Facebook");
      }
      function loginTwitter() {
        alert("Login with Twitter");
      }
    </script>
  </body>
</html>
