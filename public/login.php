<?php
session_start();

// Sudah login? Langsung ke dashboard
if (isset($_SESSION['kasir_id'])) {
    header("Location: index.php");
    exit;
}

// Ambil pesan dari session
$error       = $_SESSION['login_error']      ?? '';
$oldUsername = $_SESSION['old_username']     ?? '';
unset($_SESSION['login_error'], $_SESSION['old_username']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login – Toko Elektronik Surya Makmur</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: 'Poppins', sans-serif;
    min-height: 100vh;
    background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 55%, #2563eb 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px 16px;
}

body::before, body::after {
    content: '';
    position: fixed;
    border-radius: 50%;
    opacity: 0.07;
    pointer-events: none;
}
body::before {
    width: 500px; height: 500px;
    background: #60a5fa;
    top: -150px; right: -100px;
}
body::after {
    width: 350px; height: 350px;
    background: #818cf8;
    bottom: -80px; left: -80px;
}

/* ── Card ── */
.auth-card {
    background: white;
    border-radius: 24px;
    padding: 44px 40px 36px;
    width: 100%;
    max-width: 420px;
    box-shadow: 0 32px 64px rgba(0,0,0,0.4);
    animation: slideUp 0.4s cubic-bezier(0.16,1,0.3,1);
    position: relative;
    z-index: 1;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(32px) scale(0.98); }
    to   { opacity: 1; transform: translateY(0)   scale(1); }
}

/* ── Brand ── */
.brand { text-align: center; margin-bottom: 30px; }

.brand-icon {
    width: 78px; height: 78px;
    background: linear-gradient(135deg, #1e3a5f, #2563eb);
    border-radius: 22px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 14px;
    box-shadow: 0 8px 28px rgba(37,99,235,0.40);
    transition: transform 0.3s;
}
.brand-icon:hover { transform: rotate(-6deg) scale(1.06); }
.brand-icon i { color: white; font-size: 36px; }

.brand h1 {
    font-size: 22px; font-weight: 700;
    color: #1e3a5f; line-height: 1.2;
}
.brand p { font-size: 12.5px; color: #64748b; margin-top: 5px; }

/* ── Alert ── */
.alert {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 13px 15px; border-radius: 12px;
    font-size: 13px; margin-bottom: 22px; line-height: 1.5;
}
.alert-error   { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
.alert i { flex-shrink: 0; margin-top: 2px; }

/* ── Form ── */
.form-group { margin-bottom: 18px; }

label {
    display: block; font-size: 13px; font-weight: 600;
    color: #374151; margin-bottom: 7px;
}

.input-wrap { position: relative; }

.input-wrap .iL {
    position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
    color: #94a3b8; font-size: 15px; pointer-events: none;
    transition: color 0.2s;
}
.input-wrap:focus-within .iL { color: #2563eb; }

.input-wrap input {
    width: 100%; padding: 12px 14px 12px 43px;
    border: 1.5px solid #e2e8f0; border-radius: 12px;
    font-size: 14px; font-family: 'Poppins', sans-serif;
    color: #1e293b; background: #f8fafc;
    transition: border-color 0.2s, box-shadow 0.2s;
    outline: none;
}
.input-wrap input:focus {
    border-color: #2563eb; background: white;
    box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
}

.toggle-pw {
    position: absolute; right: 13px; top: 50%; transform: translateY(-50%);
    background: none; border: none; cursor: pointer;
    color: #94a3b8; font-size: 15px; padding: 4px;
    transition: color 0.2s;
}
.toggle-pw:hover { color: #2563eb; }

/* ── Tombol Submit ── */
.btn-submit {
    width: 100%; padding: 13px;
    background: linear-gradient(135deg, #1e3a5f, #2563eb);
    color: white; border: none; border-radius: 12px;
    font-size: 15px; font-weight: 600; font-family: 'Poppins', sans-serif;
    cursor: pointer; letter-spacing: 0.3px;
    display: flex; align-items: center; justify-content: center; gap: 9px;
    box-shadow: 0 4px 16px rgba(37,99,235,0.35);
    transition: opacity 0.2s, transform 0.15s, box-shadow 0.2s;
    margin-top: 8px;
}
.btn-submit:hover {
    opacity: 0.92; transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(37,99,235,0.45);
}
.btn-submit:active { transform: translateY(0); }

/* ── Footer ── */
.footer-link {
    text-align: center; margin-top: 24px;
    font-size: 12.5px; color: #64748b;
    background: #f8fafc; padding: 12px; border-radius: 10px;
    border: 1px dashed #cbd5e1;
}
.footer-link b { color: #1e3a5f; font-weight: 600; }
</style>
</head>
<body>

<div class="auth-card">

    <div class="brand">
        <div class="brand-icon"><i class="fa-solid fa-cash-register"></i></div>
        <h1>Sistem Point of Sale</h1>
        <p>Toko Elektronik Surya Makmur</p>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-error">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span><?= htmlspecialchars($error) ?></span>
    </div>
    <?php endif; ?>

    <form action="../process/auth_login.php" method="POST">

        <div class="form-group">
            <label for="username">Username</label>
            <div class="input-wrap">
                <i class="fa-solid fa-user iL"></i>
                <input
                    type="text" id="username" name="username"
                    placeholder="Masukkan username"
                    value="<?= htmlspecialchars($oldUsername) ?>"
                    required autofocus autocomplete="username"
                >
            </div>
        </div>

        <div class="form-group">
            <label for="pw">Password</label>
            <div class="input-wrap">
                <i class="fa-solid fa-lock iL"></i>
                <input
                    type="password" id="pw" name="password"
                    placeholder="Masukkan password" required
                    autocomplete="current-password"
                >
                <button type="button" class="toggle-pw"
                        onclick="togglePw('pw',this)" title="Tampilkan password">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn-submit">
            <i class="fa-solid fa-right-to-bracket"></i> Masuk ke Sistem
        </button>

    </form>

    <div class="footer-link">
        <i class="fa-solid fa-circle-info" style="color:#94a3b8; margin-right:4px;"></i>
        Belum punya akun? Silakan hubungi <b>Administrator</b>.
    </div>

</div>

<script>
function togglePw(id, btn) {
    const inp  = document.getElementById(id);
    const icon = btn.querySelector('i');
    if (inp.type === 'password') {
        inp.type = 'text';
        icon.className = 'fa-solid fa-eye-slash';
        btn.title = 'Sembunyikan password';
    } else {
        inp.type = 'password';
        icon.className = 'fa-solid fa-eye';
        btn.title = 'Tampilkan password';
    }
}
</script>
</body>
</html>