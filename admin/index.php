<?php
require_once __DIR__ . '/auth.php';

if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!verify_csrf($csrfToken)) {
        $error = '请求已过期，请刷新页面重试';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $clientIp = get_client_ip();

        $limit = rate_limit('login', $clientIp, 10, 900);
        if (!$limit['allowed']) {
            $mins = ceil($limit['retry_after'] / 60);
            $error = "登录尝试次数过多，请 {$mins} 分钟后重试";
        } elseif (login($username, $password)) {
            header('Location: dashboard.php');
            exit;
        } else {
            $error = '用户名或密码错误';
        }
    }
}

$admin = read_json(ADMIN_FILE);
$isDefaultPass = !empty($admin) && !empty($admin['password_hash']) && password_verify(DEFAULT_ADMIN_PASS, $admin['password_hash']);
$adminFileOk = !empty($admin) && !empty($admin['username']) && !empty($admin['password_hash']);

$token = csrf_token();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录 - 小狐务器管理后台</title>
    <link rel="stylesheet" href="../style/admin.css">
</head>
<body class="login-page">
    <div class="login-bg">
        <div class="login-pattern">
            <svg class="login-cube-svg" viewBox="0 0 800 800" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="cube" x="0" y="0" width="60" height="52" patternUnits="userSpaceOnUse">
                        <path d="M30 0 L60 15 L60 41 L30 52 L0 41 L0 15 Z" fill="none" stroke="rgba(255,255,255,0.06)" stroke-width="0.5"/>
                        <path d="M30 26 L60 15 M30 26 L0 15 M30 26 L30 52" fill="none" stroke="rgba(255,255,255,0.04)" stroke-width="0.5"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#cube)"/>
            </svg>
        </div>
    </div>

    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-avatar">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>
                <h1 class="login-title">小狐务器</h1>
                <p class="login-subtitle">管理后台登录</p>
            </div>

            <?php if ($error): ?>
                <div class="login-error">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form method="post" class="login-form" autocomplete="off">
                <input type="hidden" name="csrf_token" value="<?= e($token) ?>">
                <div class="form-field">
                    <label for="username">用户名</label>
                    <div class="input-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <input type="text" id="username" name="username" placeholder="请输入用户名" required autofocus value="<?= e($_POST['username'] ?? '') ?>">
                    </div>
                </div>
                <div class="form-field">
                    <label for="password">密码</label>
                    <div class="input-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <input type="password" id="password" name="password" placeholder="请输入密码" required>
                    </div>
                </div>
                <button type="submit" class="login-btn">
                    <span>登 录</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </button>
            </form>

            <div class="reset-link-wrap">
                <a href="javascript:void(0)" class="reset-link" id="showResetBtn">忘记密码?</a>
            </div>

            <!-- 找回密码面板 -->
            <div class="reset-panel" id="resetPanel" style="display:none">
                <div class="reset-header">
                    <a href="javascript:void(0)" class="reset-back" id="resetBackBtn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                        返回登录
                    </a>
                    <span class="reset-title">找回密码</span>
                </div>

                <!-- Step 1: 输入邮箱 -->
                <div class="reset-step" id="resetStep1">
                    <p class="reset-info">输入您在后台 SMTP 中配置的发件人邮箱，我们将发送验证码。</p>
                    <div class="form-field">
                        <label for="resetEmail">邮箱地址</label>
                        <div class="input-wrap">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                            <input type="email" id="resetEmail" placeholder="请输入邮箱" required>
                        </div>
                    </div>
                    <button type="button" class="login-btn" id="sendCodeBtn">
                        <span>发送验证码</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                    </button>
                </div>

                <!-- Step 2: 输入验证码 -->
                <div class="reset-step" id="resetStep2" style="display:none">
                    <p class="reset-info">验证码已发送，请查收邮件（5分钟内有效）。</p>
                    <div class="form-field">
                        <label for="resetCode">验证码</label>
                        <div class="input-wrap">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            <input type="text" id="resetCode" placeholder="请输入6位验证码" maxlength="6" autocomplete="off">
                        </div>
                    </div>
                    <button type="button" class="login-btn" id="verifyCodeBtn">
                        <span>验证</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </button>
                </div>

                <!-- Step 3: 设置新密码 -->
                <div class="reset-step" id="resetStep3" style="display:none">
                    <p class="reset-info">验证通过，请设置新密码。</p>
                    <div class="form-field">
                        <label for="resetNewPass">新密码</label>
                        <div class="input-wrap">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            <input type="password" id="resetNewPass" placeholder="不少于8位" required>
                        </div>
                    </div>
                    <div class="form-field">
                        <label for="resetConfirmPass">确认新密码</label>
                        <div class="input-wrap">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            <input type="password" id="resetConfirmPass" placeholder="再次输入新密码" required>
                        </div>
                    </div>
                    <button type="button" class="login-btn" id="resetPassBtn">
                        <span>重置密码</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </button>
                </div>

                <!-- 消息提示 -->
                <div class="reset-msg" id="resetMsg" style="display:none"></div>
            </div>

            <?php if (!$adminFileOk): ?>
            <p class="login-hint" style="color:#ff6b6b">⚠ 管理员配置异常，请检查 data/ 目录写入权限（chmod 755），然后删除 data/admin.json 重新访问此页面</p>
            <?php elseif ($isDefaultPass): ?>
            <p class="login-hint">首次使用默认账号: admin / admin123，请登录后立即修改密码</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
    (function () {
        var API = 'api.php';
        var loginForm = document.querySelector('.login-form');
        var resetPanel = document.getElementById('resetPanel');
        var showResetBtn = document.getElementById('showResetBtn');
        var resetBackBtn = document.getElementById('resetBackBtn');
        var step1 = document.getElementById('resetStep1');
        var step2 = document.getElementById('resetStep2');
        var step3 = document.getElementById('resetStep3');
        var resetMsg = document.getElementById('resetMsg');

        function showMsg(text, type) {
            resetMsg.textContent = text;
            resetMsg.className = 'reset-msg ' + (type || '');
            resetMsg.style.display = '';
        }
        function hideMsg() { resetMsg.style.display = 'none'; }
        function showStep(n) {
            step1.style.display = n === 1 ? '' : 'none';
            step2.style.display = n === 2 ? '' : 'none';
            step3.style.display = n === 3 ? '' : 'none';
            hideMsg();
        }

        function apiCall(action, data) {
            return fetch(API + '?action=' + action, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data || {})
            }).then(function (r) { return r.json(); });
        }

        showResetBtn.addEventListener('click', function () {
            apiCall('reset_check_smtp').then(function (r) {
                if (r.configured) {
                    loginForm.style.display = 'none';
                    resetPanel.style.display = '';
                    showStep(1);
                } else {
                    alert('未配置 SMTP 邮件发信，建议重装系统');
                }
            }).catch(function () {
                alert('请求失败，请稍后重试');
            });
        });

        resetBackBtn.addEventListener('click', function () {
            resetPanel.style.display = 'none';
            loginForm.style.display = '';
            hideMsg();
        });

        document.getElementById('sendCodeBtn').addEventListener('click', function () {
            var email = document.getElementById('resetEmail').value.trim();
            if (!email) { showMsg('请输入邮箱地址', 'error'); return; }
            var btn = this;
            btn.disabled = true;
            btn.querySelector('span').textContent = '发送中...';
            apiCall('reset_send_code', { email: email }).then(function (r) {
                if (r.success) {
                    showStep(2);
                    showMsg(r.message, 'success');
                } else {
                    showMsg(r.message, 'error');
                }
            }).catch(function () {
                showMsg('请求失败', 'error');
            }).finally(function () {
                btn.disabled = false;
                btn.querySelector('span').textContent = '发送验证码';
            });
        });

        document.getElementById('verifyCodeBtn').addEventListener('click', function () {
            var code = document.getElementById('resetCode').value.trim();
            if (!code || code.length !== 6) { showMsg('请输入6位验证码', 'error'); return; }
            showStep(3);
        });

        document.getElementById('resetPassBtn').addEventListener('click', function () {
            var code = document.getElementById('resetCode').value.trim();
            var newPass = document.getElementById('resetNewPass').value;
            var confirmPass = document.getElementById('resetConfirmPass').value;
            if (newPass.length < 8) { showMsg('新密码不能少于8位', 'error'); return; }
            if (newPass !== confirmPass) { showMsg('两次密码输入不一致', 'error'); return; }
            var btn = this;
            btn.disabled = true;
            btn.querySelector('span').textContent = '重置中...';
            apiCall('reset_password', { code: code, new_password: newPass }).then(function (r) {
                if (r.success) {
                    showMsg(r.message, 'success');
                    setTimeout(function () {
                        resetPanel.style.display = 'none';
                        loginForm.style.display = '';
                        hideMsg();
                    }, 2000);
                } else {
                    showMsg(r.message, 'error');
                }
            }).catch(function () {
                showMsg('请求失败', 'error');
            }).finally(function () {
                btn.disabled = false;
                btn.querySelector('span').textContent = '重置密码';
            });
        });
    })();
    </script>
</body>
</html>
