<nav>
      <div class="left_nav">
        <div class="logo">
          <img
            src="./assets/attribute myplanet/Logo My PlanEt.png"
            alt="My PlanET"
          />
          <p>My PlanET</p>
        </div>
      </div>

      <div class="right_nav">
        <div class="masuk-daftar">
          <a href="#">Masuk</a>
          <a href="#">Daftar</a>
        </div>
        <div class="Dropdown" style="display: none">
          <div class="profil">
            <button>Profil</button>
          </div>
          <div class="Content-dropdown" style="display: none">
            <a href="#">Dashboard</a>
            <a href="#">Keluar</a>
          </div>
        </div>
      </div>
    </nav>

    <!-- Dashboard Layout -->
    <div class="dashboard">
      <!-- Sidebar Menu -->
      <aside class="sidebar">
        <div class="menu">
          <ul>
            <li><a href="#home">Home</a></li>
            <li><a href="#profil">Profile</a></li>
            <li><a href="#transaksi">Riwayat Transaksi</a></li>
            <li><a href="#">Logout</a></li>
          </ul>
        </div>
      </aside>

      <!-- Main Content -->
      <main class="content">
        <!-- Header Section -->
        <header class="header">
          <h1>Welcome! <?php echo $username; ?></h1>
          <div class="card_profil">
            <img
              src="./assets/Alpi Darul Hakim.png"
              alt="User Profile"
              class="profile_pic"
            />
            <div class="user_info">
              <h2>Alpi Darul Hakim</h2>
              <p>User / Event Organizer</p>
            </div>
          </div>
        </header>

        <section id="home" class="content-section">
          <h2>Transaksi</h2>

          <div class="contentheader">
            <div class="total_transaksi">
              <h2>Total Transaksi</h2>
              <div class="total">
                <p>Rp. 1.234.567.890</p>
              </div>
            </div>
          </div>
          <div class="contentheader">
            <div class="total_penjualan">
              <h2>Total Penjualan</h2>
              <div class="total">
                <p>Rp. 987.654.321</p>
              </div>
            </div>
          </div>
        </section>


        <section id="profil" class="profil content-section">
          <div class="profile-card">
            <div class="profile-photo">
              <img src="./assets/Alpi Darul Hakim.png" alt="User Photo" />
            </div>
            <div class="profile-info">
              <h2 class="name"><?php echo $username?></h2>
              <p class="username">@Alpi Darul Hakim</p>
              <p class="email">alpidarulhakim@example.com</p>
              <p class="phone">+123456789</p>
              <p class="address">123 Main St, City, Country</p>
              <p class="gender">Gender: M</p>
              <p class="usia">Age: 30</p>
              <p class="institusi">Affiliation: XYZ Institute</p>
              <p class="event-preference">Event Preference: Wedding</p>
              <p class="budget">Budget: $5000</p>
              <p class="date-event">Event Date: 2024-09-20</p>
            </div>
          </div>
        </section>
      </main>
    </div>

    <script>

<div class="profile-section">
        <div><span>Username:</span> <?php echo $user['username']; ?></div>
        <div><span>Nama:</span> <?php echo $user['name']; ?></div>
        <div><span>Email:</span> <?php echo $user['email']; ?></div>
        <div><span>Telepon:</span> <?php echo $user['phone']; ?></div>
        <div><span>Gender:</span> <?php echo $user['gender']; ?></div>
        <div><span>Usia:</span> <?php echo $user['usia']; ?></div>
        <div><span>Institusi Afiliasi:</span> <?php echo $user['institusi_afiliasi']; ?></div>
        <div><span>Preferensi Event:</span> <?php echo $user['event_preference']; ?></div>
        <div><span>Budget:</span> Rp. <?php echo number_format($user['budget'], 2); ?></div>
    </div>


    <!-- Tombol Edit Profil -->
            <div id="dashboard" class="content_section">
        <div class="profile-section">
          <div class="sampul-profil">
            <img src="../assets/Background.jpg" alt="Sampul Profil" />
          </div>
          <div class="info-profil">
            <img src="../assets/Alpi Darul Hakim.png" alt="Foto Profil" />
            <p><?php echo $vendor['username']; ?></p>
          </div>
          <div class="profile-details">
            <div class="profile-details1">
              <?php if (!empty($vendor['name'])): ?>
              <div class="detail-item">
                <span class="label">Nama:</span>
                <span class="value"><?php echo $vendor['name']; ?></span>
              </div>
              <?php endif; ?>

              <?php if (!empty($vendor['email'])): ?>
              <div class="detail-item">
                <span class="label">Email:</span>
                <span class="value"><?php echo $vendor['email']; ?></span>
              </div>
              <?php endif; ?>

              <?php if (!empty($vendor['phone_vendor'])): ?>
              <div class="detail-item">
                <span class="label">Telepon Vendor:</span>
                <span class="value"><?php echo $vendor['phone_vendor']; ?></span>
              </div>
              <?php endif; ?>

              <?php if (!empty($vendor['email'])): ?>
              <div class="detail-item">
                <span class="label">email Vendor:</span>
                <span class="value"><?php echo $vendor['email']; ?></span>
              </div>
              <?php endif; ?>
            </div>

            
            <?php if (!empty($vendor['name_owner'])): ?>
              <div class="detail-item">
                <span class="label">Nama Pemilik:</span>
                <span class="value"><?php echo $vendor['name_owner']; ?></span>
              </div>
              <?php endif; ?>

            <div class="profile-details2">
              <?php if (!empty($vendor['phone_owner'])): ?>
              <div class="detail-item">
                <span class="label">Telepon Pemilik:</span>
                <span class="value"><?php echo $vendor['phone_owner']; ?></span>
              </div>
              <?php endif; ?>

              <?php if (!empty($vendor['email_owner'])): ?>
              <div class="detail-item">
                <span class="label">Email Pemilik:</span>
                <span class="value"><?php echo $vendor['email_owner']; ?></span>
              </div>
              <?php endif; ?>

              <?php if (!empty($vendor['address'])): ?>
              <div class="detail-item">
                <span class="label">Alamat:</span>
                <span class="value"><?php echo $vendor['address']; ?></span>
              </div>
              <?php endif; ?>

              <?php if (!empty($vendor['date_operasional'])): ?>
              <div class="detail-item">
                <span class="label">Tanggal Operasional:</span>
                <span class="value">Rp. <?php echo date($vendor['date_operasional']); ?></span>
              </div>
              <?php endif; ?>

              <?php if (!empty($vendor['jenis_bisnis'])): ?>
              <div class="detail-item">
                <span class="label">Tanggal Operasional:</span>
                <span class="value">Rp. <?php echo date($vendor['Jenis_bisnis']); ?></span>
              </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>