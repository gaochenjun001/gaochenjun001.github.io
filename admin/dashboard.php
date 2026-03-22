<?php
require_once __DIR__ . '/auth.php';
require_login();
$content = read_json(CONTENT_FILE) ?: [];
$admin = read_json(ADMIN_FILE) ?: [];
$hideAd = !empty($admin['hide_ad']);

$adTamperLocked = false;
$adTamperDeadline = 0;
$adTamperServerTs = time();
if (!empty($admin['ad_tamper_ts'])) {
    $adTamperDeadline = $admin['ad_tamper_ts'] + 300;
    if (time() < $adTamperDeadline) {
        $adTamperLocked = true;
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title>管理后台 - 小狐务器</title>
    <link rel="stylesheet" href="../style/admin.css">
</head>
<body>
    <!-- 背景 -->
    <div class="admin-bg">
        <div class="admin-pattern">
            <svg class="admin-cube-svg" viewBox="0 0 800 800" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="cube" x="0" y="0" width="60" height="52" patternUnits="userSpaceOnUse">
                        <path d="M30 0 L60 15 L60 41 L30 52 L0 41 L0 15 Z" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="0.5"/>
                        <path d="M30 26 L60 15 M30 26 L0 15 M30 26 L30 52" fill="none" stroke="rgba(255,255,255,0.03)" stroke-width="0.5"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#cube)"/>
            </svg>
        </div>
    </div>
    <div class="admin-blur"></div>

    <div class="admin-wrap">
        <!-- 顶部栏 -->
        <header class="admin-header">
            <div class="admin-header-top">
                <div class="admin-brand">
                    <span class="brand-name">小狐务器</span>
                    <span class="brand-badge">管理后台</span>
                </div>
                <div class="admin-actions">
                    <a href="../" target="_blank" class="action-link">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                        <span>查看前台</span>
                    </a>
                    <form method="post" action="logout.php" style="display:inline">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <button type="submit" class="action-link action-logout" style="border:none;background:none;cursor:pointer;font:inherit;color:inherit;padding:0;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            <span>退出</span>
                        </button>
                    </form>
                </div>
            </div>
            <?php if (!$hideAd): ?>
            <!-- 广告横幅 -->
            <div class="ad-banner" id="adBanner">
                <a href="https://www.rainyun.com/freehost_" target="_blank" rel="noopener" class="ad-banner-link">
                    <div class="ad-banner-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M18 10h-1.26A8 8 0 1 0 9 20h9a5 5 0 0 0 0-10z"/></svg>
                    </div>
                    <div class="ad-banner-content">
                        <span class="ad-banner-title">雨云</span>
                        <span class="ad-banner-sep">·</span>
                        <span class="ad-banner-desc">服务上万用户低成本上云！</span>
                    </div>
                    <div class="ad-banner-cta">
                        了解详情
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </div>
                </a>
                <button type="button" class="ad-banner-close" id="adCloseBtn" title="关闭广告">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
                <span class="ad-banner-badge">AD</span>
            </div>
            <?php endif; ?>
        </header>

        <div class="admin-body">
            <!-- 侧边导航 -->
            <nav class="admin-nav">
                <div class="nav-item active" data-tab="site">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>
                    <span>站点设置</span>
                </div>
                <div class="nav-item" data-tab="intro">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    <span>个人简介</span>
                </div>
                <div class="nav-item" data-tab="skills">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                    <span>技能管理</span>
                </div>
                <div class="nav-item" data-tab="projects">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                    <span>项目管理</span>
                </div>
                <div class="nav-item" data-tab="contact">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    <span>联系方式</span>
                </div>
                <div class="nav-divider"></div>
                <div class="nav-item" data-tab="smtp">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                    <span>SMTP 邮件</span>
                </div>
                <div class="nav-item" data-tab="messages">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22z"/></svg>
                    <span>消息管理</span>
                    <span class="nav-badge" id="msgBadge" style="display:none">0</span>
                </div>
                <div class="nav-divider"></div>
                <div class="nav-item" data-tab="password">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    <span>修改密码</span>
                </div>
                <div class="nav-divider"></div>
                <div class="nav-item nav-item-danger" data-tab="nuke">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                    <span>一键跑路</span>
                </div>

                <div class="nav-footer">
                    <span>小狐务器 · 个人主页系统</span>
                </div>
            </nav>

            <!-- 主内容区 -->
            <main class="admin-main">

                <!-- ========== 站点设置 ========== -->
                <section class="tab-panel active" id="tab-site">
                    <div class="panel-header">
                        <h2>站点设置</h2>
                        <p class="panel-desc">修改头像、标题、副标题和像素文字内容</p>
                    </div>

                    <!-- 头像上传 -->
                    <div class="glass-card avatar-upload-card">
                        <div class="avatar-upload-wrap">
                            <div class="avatar-preview" id="avatarPreview">
                                <div class="avatar-preview-placeholder" id="avatarPlaceholder">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                </div>
                                <img id="avatarImg" class="avatar-preview-img" src="" alt="头像" style="display:none">
                            </div>
                            <div class="avatar-upload-info">
                                <h4>头像设置</h4>
                                <p class="form-hint">支持 JPG/PNG/GIF/WebP，不超过 5MB，将自动裁剪为正方形</p>
                                <div class="avatar-upload-actions">
                                    <label class="btn btn-primary btn-sm" for="avatarFile">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                        上传头像
                                    </label>
                                    <button type="button" class="btn btn-outline btn-sm" id="deleteAvatarBtn" style="display:none">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                        删除
                                    </button>
                                    <input type="file" id="avatarFile" accept="image/*" style="display:none">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 背景设置 -->
                    <div class="glass-card" style="margin-top: 14px;">
                        <h4 style="font-size:15px;font-weight:600;margin-bottom:14px;">背景设置</h4>

                        <!-- 背景模式选择 -->
                        <div class="form-group">
                            <label>背景模式</label>
                            <div class="bg-mode-selector" id="bgModeSelector">
                                <label class="bg-mode-option active" data-mode="default">
                                    <div class="bg-mode-preview bg-mode-default-preview"></div>
                                    <span>默认纹理</span>
                                </label>
                                <label class="bg-mode-option" data-mode="image">
                                    <div class="bg-mode-preview bg-mode-image-preview" id="bgModeImageThumb"></div>
                                    <span>自定义图片</span>
                                </label>
                            </div>
                        </div>

                        <!-- 背景图片上传（仅图片模式显示） -->
                        <div class="bg-image-section" id="bgImageSection" style="display:none;">
                            <div class="form-group">
                                <label>背景图片</label>
                                <div class="bg-upload-row">
                                    <div class="bg-thumb" id="bgThumb">
                                        <div class="bg-thumb-empty" id="bgThumbEmpty">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                        </div>
                                        <img id="bgThumbImg" class="bg-thumb-img" src="" alt="背景预览" style="display:none">
                                    </div>
                                    <div class="bg-upload-actions">
                                        <label class="btn btn-primary btn-sm" for="bgFile">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                            上传图片
                                        </label>
                                        <button type="button" class="btn btn-outline btn-sm" id="deleteBgBtn" style="display:none">删除</button>
                                        <input type="file" id="bgFile" accept="image/*" style="display:none">
                                    </div>
                                </div>
                                <span class="form-hint">支持 JPG/PNG/GIF/WebP，不超过 10MB</span>
                            </div>
                        </div>

                        <!-- 模糊度和暗度滑块 -->
                        <div class="form-grid">
                            <div class="form-group">
                                <label>背景模糊度: <span id="blurValue">6</span>px</label>
                                <input type="range" id="bgBlurRange" name="bg_blur" min="0" max="30" step="1" value="6" class="range-input">
                            </div>
                            <div class="form-group">
                                <label>遮罩不透明度: <span id="opacityValue">70</span>%</label>
                                <input type="range" id="bgOpacityRange" name="bg_opacity" min="0" max="100" step="5" value="70" class="range-input">
                                <span class="form-hint">数值越高背景越暗，文字越清晰</span>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn btn-primary" id="saveBgBtn">保存背景设置</button>
                        </div>
                    </div>

                    <form class="glass-card" id="siteForm" style="margin-top: 14px;">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>标题前缀</label>
                                <input type="text" name="title_prefix" value="<?= e($content['title_prefix'] ?? '') ?>" placeholder="欢迎来到">
                            </div>
                            <div class="form-group">
                                <label>主标题</label>
                                <input type="text" name="title" value="<?= e($content['title'] ?? '') ?>" placeholder="小狐务器">
                            </div>
                            <div class="form-group">
                                <label>副标题前缀</label>
                                <input type="text" name="subtitle_prefix" value="<?= e($content['subtitle_prefix'] ?? '') ?>" placeholder="Welcome...">
                            </div>
                            <div class="form-group">
                                <label>副标题名称</label>
                                <input type="text" name="subtitle_name" value="<?= e($content['subtitle_name'] ?? '') ?>" placeholder="Xiaohu Server">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>像素横幅文字</label>
                            <input type="text" name="pixel_text" value="<?= e($content['pixel_text'] ?? '') ?>" placeholder="我爱雨云" maxlength="4">
                            <span class="form-hint">显示在标题下方的像素点阵动画文字（最多 4 个字）</span>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">保存设置</button>
                        </div>
                    </form>

                    <!-- 渐变色设置 -->
                    <div class="glass-card" style="margin-top: 14px;">
                        <h4 style="font-size:15px;font-weight:600;margin-bottom:14px;">标题渐变色</h4>

                        <!-- 实时预览 -->
                        <div class="gradient-preview" id="gradientPreview">
                            <span class="gradient-preview-text" id="gradientPreviewText">小狐务器</span>
                        </div>

                        <!-- 色块列表 -->
                        <div class="form-group">
                            <label>渐变颜色（点击修改，最少 2 个）</label>
                            <div class="gradient-colors" id="gradientColors"></div>
                            <div class="gradient-color-actions">
                                <button type="button" class="btn btn-outline btn-sm" id="addColorBtn">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                    添加颜色
                                </button>
                                <button type="button" class="btn btn-outline btn-sm" id="resetColorsBtn">恢复默认</button>
                            </div>
                        </div>

                        <!-- 预设方案 -->
                        <div class="form-group">
                            <label>快速预设</label>
                            <div class="gradient-presets" id="gradientPresets">
                                <button type="button" class="gradient-preset" data-colors="#ff6b6b,#ffd93d,#6bcb77,#4d96ff,#9b59b6" title="彩虹">
                                    <span style="background:linear-gradient(90deg,#ff6b6b,#ffd93d,#6bcb77,#4d96ff,#9b59b6)"></span>
                                </button>
                                <button type="button" class="gradient-preset" data-colors="#f093fb,#f5576c" title="粉紫">
                                    <span style="background:linear-gradient(90deg,#f093fb,#f5576c)"></span>
                                </button>
                                <button type="button" class="gradient-preset" data-colors="#4facfe,#00f2fe" title="天蓝">
                                    <span style="background:linear-gradient(90deg,#4facfe,#00f2fe)"></span>
                                </button>
                                <button type="button" class="gradient-preset" data-colors="#43e97b,#38f9d7" title="薄荷">
                                    <span style="background:linear-gradient(90deg,#43e97b,#38f9d7)"></span>
                                </button>
                                <button type="button" class="gradient-preset" data-colors="#fa709a,#fee140" title="日落">
                                    <span style="background:linear-gradient(90deg,#fa709a,#fee140)"></span>
                                </button>
                                <button type="button" class="gradient-preset" data-colors="#a18cd1,#fbc2eb" title="淡紫">
                                    <span style="background:linear-gradient(90deg,#a18cd1,#fbc2eb)"></span>
                                </button>
                                <button type="button" class="gradient-preset" data-colors="#ffecd2,#fcb69f" title="暖橙">
                                    <span style="background:linear-gradient(90deg,#ffecd2,#fcb69f)"></span>
                                </button>
                                <button type="button" class="gradient-preset" data-colors="#ff9a9e,#fecfef,#fdfcfb" title="樱花">
                                    <span style="background:linear-gradient(90deg,#ff9a9e,#fecfef,#fdfcfb)"></span>
                                </button>
                                <button type="button" class="gradient-preset" data-colors="#667eea,#764ba2" title="靛蓝">
                                    <span style="background:linear-gradient(90deg,#667eea,#764ba2)"></span>
                                </button>
                                <button type="button" class="gradient-preset" data-colors="#f7971e,#ffd200" title="金黄">
                                    <span style="background:linear-gradient(90deg,#f7971e,#ffd200)"></span>
                                </button>
                            </div>
                        </div>

                        <!-- 动画开关 -->
                        <div class="form-group">
                            <label class="toggle-label">
                                <span>流动动画效果</span>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="gradientAnimateToggle" checked>
                                    <span class="toggle-slider"></span>
                                </label>
                            </label>
                            <span class="form-hint">开启后渐变色会持续流动，关闭则为静态渐变</span>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn btn-primary" id="saveGradientBtn">保存渐变设置</button>
                        </div>
                    </div>
                </section>

                <!-- ========== 个人简介 ========== -->
                <section class="tab-panel" id="tab-intro">
                    <div class="panel-header">
                        <h2>个人简介</h2>
                        <p class="panel-desc">编辑首页的个人简介段落内容</p>
                    </div>
                    <form class="glass-card" id="introForm">
                        <div class="form-group">
                            <label>简介内容</label>
                            <textarea name="intro" rows="6" placeholder="在这里介绍你自己..."><?= e($content['intro'] ?? '') ?></textarea>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">保存简介</button>
                        </div>
                    </form>
                </section>

                <!-- ========== 技能管理 ========== -->
                <section class="tab-panel" id="tab-skills">
                    <div class="panel-header">
                        <h2>技能管理</h2>
                        <p class="panel-desc">添加、删除和排序你的技能标签</p>
                    </div>
                    <div class="glass-card">
                        <div class="skill-input-row">
                            <input type="text" id="newSkill" placeholder="输入新技能名称..." class="skill-input">
                            <button type="button" class="btn btn-primary" id="addSkillBtn">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                添加
                            </button>
                        </div>
                        <div class="skills-list" id="skillsList">
                            <?php foreach (($content['skills'] ?? []) as $skill): ?>
                                <div class="skill-item">
                                    <span class="skill-text"><?= e($skill) ?></span>
                                    <button type="button" class="skill-remove" title="删除">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn btn-primary" id="saveSkillsBtn">保存技能</button>
                        </div>
                    </div>
                </section>

                <!-- ========== 项目管理 ========== -->
                <section class="tab-panel" id="tab-projects">
                    <div class="panel-header">
                        <h2>项目管理</h2>
                        <p class="panel-desc">管理首页展示的项目卡片</p>
                    </div>
                    <div class="glass-card">
                        <div class="projects-editor" id="projectsEditor">
                            <?php foreach (($content['projects'] ?? []) as $i => $proj): ?>
                                <div class="project-edit-card" data-index="<?= $i ?>">
                                    <div class="project-edit-header">
                                        <span class="project-edit-num">#<?= $i + 1 ?></span>
                                        <button type="button" class="btn-icon project-remove" title="删除项目">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                        </button>
                                    </div>
                                    <div class="form-grid">
                                        <div class="form-group">
                                            <label>项目名称</label>
                                            <input type="text" name="proj_title" value="<?= e($proj['title']) ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>图标类型</label>
                                            <select name="proj_icon">
                                                <?php
                                                $icons = ['globe' => '地球', 'code' => '代码', 'layout' => '布局', 'sparkle' => '星光', 'server' => '服务器', 'palette' => '调色板', 'terminal' => '终端', 'book' => '书本'];
                                                foreach ($icons as $val => $label):
                                                ?>
                                                    <option value="<?= $val ?>" <?= ($proj['icon'] ?? '') === $val ? 'selected' : '' ?>><?= $label ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>项目描述</label>
                                        <input type="text" name="proj_desc" value="<?= e($proj['desc']) ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>项目链接 <span style="opacity:.5;font-weight:400">（选填，留空则不跳转）</span></label>
                                        <input type="url" name="proj_link" value="<?= e($proj['link'] ?? '') ?>" placeholder="https://">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-outline add-project-btn" id="addProjectBtn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            添加项目
                        </button>
                        <div class="form-actions">
                            <button type="button" class="btn btn-primary" id="saveProjectsBtn">保存项目</button>
                        </div>
                    </div>
                </section>

                <!-- ========== 联系方式 ========== -->
                <section class="tab-panel" id="tab-contact">
                    <div class="panel-header">
                        <h2>联系方式</h2>
                        <p class="panel-desc">设置首页展示的联系信息</p>
                    </div>
                    <form class="glass-card" id="contactForm">
                        <div class="form-group">
                            <label>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" class="label-icon"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                邮箱
                            </label>
                            <input type="email" name="email" value="<?= e($content['contact']['email'] ?? '') ?>" placeholder="your@email.com">
                        </div>
                        <div class="form-group">
                            <label>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" class="label-icon"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22z"/></svg>
                                微信号
                            </label>
                            <input type="text" name="wechat" value="<?= e($content['contact']['wechat'] ?? '') ?>" placeholder="your_wechat">
                        </div>
                        <div class="form-group">
                            <label>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" class="label-icon"><path d="M15 22v-4a4.8 4.8 0 0 0-1-3.5c3 0 6-2 6-5.5.08-1.25-.27-2.48-1-3.5.28-1.15.28-2.35 0-3.5 0 0-1 0-3 1.5-2.64-.5-5.36-.5-8 0C6 2 5 2 5 2c-.3 1.15-.3 2.35 0 3.5A5.403 5.403 0 0 0 4 9c0 3.5 3 5.5 6 5.5-.39.49-.68 1.05-.85 1.65-.17.6-.22 1.23-.15 1.85v4"/><path d="M9 18c-4.51 2-5-2-7-2"/></svg>
                                GitHub
                            </label>
                            <input type="text" name="github" value="<?= e($content['contact']['github'] ?? '') ?>" placeholder="github.com/username">
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">保存联系方式</button>
                        </div>
                    </form>
                </section>

                <!-- ========== SMTP 邮件 ========== -->
                <section class="tab-panel" id="tab-smtp">
                    <div class="panel-header">
                        <h2>SMTP 邮件设置</h2>
                        <p class="panel-desc">配置 SMTP 服务器，用于接收留言通知和回复访客邮件</p>
                    </div>
                    <form class="glass-card" id="smtpForm">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>SMTP 服务器</label>
                                <input type="text" name="host" placeholder="smtp.example.com">
                            </div>
                            <div class="form-group form-grid-half">
                                <div class="form-group">
                                    <label>端口</label>
                                    <input type="number" name="port" value="587" placeholder="587">
                                </div>
                                <div class="form-group">
                                    <label>加密方式</label>
                                    <select name="encryption">
                                        <option value="tls">TLS</option>
                                        <option value="ssl">SSL</option>
                                        <option value="none">无</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>SMTP 用户名</label>
                                <input type="text" name="username" placeholder="your@email.com">
                            </div>
                            <div class="form-group">
                                <label>SMTP 密码</label>
                                <input type="password" name="password" placeholder="留空表示不修改">
                                <span class="form-hint">部分邮箱需使用授权码而非登录密码</span>
                            </div>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>发件人名称</label>
                                <input type="text" name="from_name" placeholder="小狐务器">
                            </div>
                            <div class="form-group">
                                <label>发件人邮箱</label>
                                <input type="email" name="from_email" placeholder="your@email.com">
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">保存配置</button>
                            <button type="button" class="btn btn-outline" id="testSmtpBtn">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                                测试连接
                            </button>
                        </div>
                        <div class="smtp-log" id="smtpLog" style="display:none">
                            <h4>连接日志</h4>
                            <pre id="smtpLogContent"></pre>
                        </div>
                    </form>
                </section>

                <!-- ========== 消息管理 ========== -->
                <section class="tab-panel" id="tab-messages">
                    <div class="panel-header">
                        <h2>消息管理</h2>
                        <p class="panel-desc">查看访客留言，通过 SMTP 回复邮件</p>
                    </div>

                    <!-- 免打扰设置 -->
                    <div class="glass-card dnd-card" style="margin-bottom: 14px;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="toggle-label">
                                <div class="dnd-label-content">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" class="label-icon"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                                    <div>
                                        <span>消息免打扰</span>
                                        <span class="dnd-status-text" id="dndStatusText">关闭 · 新消息将发送邮件通知</span>
                                    </div>
                                </div>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="dndToggle">
                                    <span class="toggle-slider"></span>
                                </label>
                            </label>
                        </div>
                    </div>

                    <div class="glass-card">
                        <div class="messages-toolbar">
                            <button type="button" class="btn btn-outline btn-sm" id="refreshMsgsBtn">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                                刷新
                            </button>
                        </div>
                        <div class="messages-list" id="messagesList">
                            <div class="messages-empty">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                <p>暂无消息</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ========== 修改密码 ========== -->
                <section class="tab-panel" id="tab-password">
                    <div class="panel-header">
                        <h2>修改密码</h2>
                        <p class="panel-desc">修改管理后台的登录密码</p>
                    </div>
                    <form class="glass-card" id="passwordForm" style="max-width: 480px;">
                        <div class="form-group">
                            <label>当前密码</label>
                            <input type="password" name="old_password" required placeholder="请输入当前密码">
                        </div>
                        <div class="form-group">
                            <label>新密码</label>
                            <input type="password" name="new_password" required placeholder="不少于8位">
                        </div>
                        <div class="form-group">
                            <label>确认新密码</label>
                            <input type="password" name="confirm_password" required placeholder="再次输入新密码">
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">修改密码</button>
                        </div>
                    </form>

                    <!-- 广告设置 -->
                    <div class="glass-card" style="margin-top: 14px; max-width: 480px;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="toggle-label">
                                <span>永久关闭个性化广告</span>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="hideAdToggle" <?= $hideAd ? 'checked' : '' ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </label>
                            <span class="form-hint">开启后将不再显示后台顶部的推广信息</span>
                        </div>
                    </div>
                </section>

                <!-- ========== 一键跑路 ========== -->
                <section class="tab-panel" id="tab-nuke">
                    <div class="panel-header">
                        <h2 class="danger-title">一键跑路</h2>
                        <p class="panel-desc">永久删除网站目录下的所有文件，此操作不可恢复</p>
                    </div>

                    <!-- 模式选择 -->
                    <div class="glass-card" style="margin-bottom:14px;">
                        <h4 style="font-size:15px;font-weight:600;margin-bottom:14px;">执行模式</h4>
                        <div class="nuke-mode-selector">
                            <label class="nuke-mode-card active" data-mode="manual">
                                <div class="nuke-mode-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                                </div>
                                <div class="nuke-mode-info">
                                    <strong>手动模式</strong>
                                    <span>点击按钮后手动确认执行</span>
                                </div>
                            </label>
                            <label class="nuke-mode-card" data-mode="auto">
                                <div class="nuke-mode-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                </div>
                                <div class="nuke-mode-info">
                                    <strong>自动模式</strong>
                                    <span>到达设定时间后自动执行</span>
                                </div>
                            </label>
                        </div>

                        <!-- 自动模式设置 -->
                        <div class="nuke-auto-settings" id="nukeAutoSettings" style="display:none;">
                            <div class="form-group" style="margin-top:14px;">
                                <label>自动执行倒计时（天）</label>
                                <div class="nuke-days-input">
                                    <input type="number" id="nukeDaysInput" min="1" max="365" value="7" placeholder="天数">
                                    <span class="nuke-days-suffix">天后自动执行</span>
                                </div>
                                <span class="form-hint">设定后，从保存时刻起倒计时，到期后前端访问时将自动触发删除</span>
                            </div>
                            <div class="nuke-countdown-status" id="nukeCountdownStatus" style="display:none;">
                                <div class="nuke-countdown-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                </div>
                                <div class="nuke-countdown-info">
                                    <span class="nuke-countdown-label">自动跑路倒计时</span>
                                    <span class="nuke-countdown-time" id="nukeCountdownTime">--</span>
                                </div>
                                <button type="button" class="btn btn-outline btn-sm" id="nukeCancelAutoBtn">取消定时</button>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn btn-primary" id="saveNukeModeBtn">保存模式设置</button>
                        </div>
                    </div>

                    <!-- 跑路后跳转设置 -->
                    <div class="glass-card" style="margin-bottom:14px;">
                        <h4 style="font-size:15px;font-weight:600;margin-bottom:14px;">跑路后跳转</h4>
                        <div class="form-group">
                            <label class="toggle-label">
                                <span>执行后自动跳转到指定链接</span>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="nukeRedirectToggle">
                                    <span class="toggle-slider"></span>
                                </label>
                            </label>
                            <span class="form-hint">开启后，跑路执行完成会自动跳转到下方设置的链接</span>
                        </div>
                        <div id="nukeRedirectSection" style="display:none;">
                            <div class="form-group">
                                <label>跳转链接</label>
                                <input type="url" id="nukeRedirectUrl" placeholder="https://example.com">
                                <span class="form-hint">跑路成功后浏览器将自动跳转到此地址</span>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn btn-primary" id="saveNukeRedirectBtn">保存跳转设置</button>
                        </div>
                    </div>

                    <!-- 危险区域 -->
                    <div class="glass-card danger-zone">
                        <div class="danger-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                                <line x1="12" y1="9" x2="12" y2="13"/>
                                <line x1="12" y1="17" x2="12.01" y2="17"/>
                            </svg>
                        </div>
                        <h3 class="danger-heading">危险操作</h3>
                        <p class="danger-text">
                            点击下方按钮将 <strong>永久删除</strong> 网站根目录下的所有文件和文件夹，包括：
                        </p>
                        <ul class="danger-list">
                            <li>所有前端页面和样式文件</li>
                            <li>管理后台程序</li>
                            <li>上传的头像和背景图片</li>
                            <li>所有留言数据和配置</li>
                        </ul>
                        <p class="danger-text danger-warn">
                            此操作执行后无法撤销，所有数据将永久丢失！
                        </p>
                        <button type="button" class="btn btn-nuke" id="nukeBtn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                            立即执行跑路
                        </button>
                    </div>
                </section>

            </main>
        </div>
    </div>

    <!-- 跑路确认弹窗 -->
    <div class="modal-overlay" id="nukeModal">
        <div class="modal-card nuke-modal">
            <div class="modal-header">
                <h3 class="danger-title">最终确认</h3>
                <button type="button" class="modal-close" id="closeNukeModal">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div class="nuke-confirm-body">
                <div class="nuke-warn-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="15" y1="9" x2="9" y2="15"/>
                        <line x1="9" y1="9" x2="15" y2="15"/>
                    </svg>
                </div>
                <p class="nuke-confirm-text">你即将删除网站的所有文件，此操作<strong>不可恢复</strong>。</p>
                <p class="nuke-confirm-text">请在下方输入 <code>确认删除</code> 并验证密码以继续：</p>
                <div class="form-group">
                    <input type="text" id="nukeConfirmInput" placeholder="请输入"确认删除"" autocomplete="off" spellcheck="false">
                </div>
                <div class="form-group">
                    <input type="password" id="nukePasswordInput" placeholder="请输入当前登录密码" autocomplete="off">
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-nuke" id="nukeConfirmBtn" disabled>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                        永久删除所有文件
                    </button>
                    <button type="button" class="btn btn-outline" id="nukeCancelBtn">取消</button>
                </div>
            </div>
        </div>
    </div>

    <!-- 回复弹窗 -->
    <div class="modal-overlay" id="replyModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3>回复邮件</h3>
                <button type="button" class="modal-close" id="closeReplyModal">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <form id="replyForm">
                <input type="hidden" name="msg_id" id="replyMsgId">
                <div class="form-group">
                    <label>收件人</label>
                    <input type="email" name="to" id="replyTo" readonly>
                </div>
                <div class="form-group">
                    <label>主题</label>
                    <input type="text" name="subject" id="replySubject" placeholder="回复主题">
                </div>
                <div class="form-group">
                    <label>回复内容</label>
                    <textarea name="body" id="replyBody" rows="6" placeholder="输入回复内容..." required></textarea>
                </div>
                <div class="form-group">
                    <label>附带图片 <span style="opacity:.5;font-weight:400">（可选，将作为邮件附件发送）</span></label>
                    <div class="reply-image-area" id="replyImageArea">
                        <input type="file" id="replyImageFile" accept="image/jpeg,image/png,image/gif,image/webp" style="display:none">
                        <div class="reply-image-add" id="replyImageAdd">
                            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        </div>
                        <div class="reply-image-thumb" id="replyImageThumb" style="display:none">
                            <img id="replyImagePreviewImg" src="" alt="预览">
                            <button type="button" class="reply-image-remove" id="replyImageRemove" title="移除">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">发送回复</button>
                    <button type="button" class="btn btn-outline" id="cancelReply">取消</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast 通知 -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- 广告篡改锁定遮罩 -->
    <div class="tamper-overlay" id="tamperOverlay" style="display:none">
        <div class="tamper-card">
            <div class="tamper-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/>
                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
            </div>
            <h2 class="tamper-title">后台已锁定</h2>
            <p class="tamper-desc">检测到广告链接被篡改，请立即修改回原始链接。</p>
            <div class="tamper-countdown">
                <span class="tamper-countdown-label">自动跑路倒计时</span>
                <span class="tamper-countdown-time" id="tamperCountdown">--:--</span>
            </div>
            <p class="tamper-warn">超时未恢复将自动删除网站所有文件！</p>
        </div>
    </div>

    <script>
        window.__TAMPER_STATE__ = {
            locked: <?= $adTamperLocked ? 'true' : 'false' ?>,
            deadline_ts: <?= intval($adTamperDeadline) ?>,
            server_ts: <?= intval($adTamperServerTs) ?>
        };
    </script>
    <script src="script.js"></script>
</body>
</html>
