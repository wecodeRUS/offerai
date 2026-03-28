<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
boot_session();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="<?= htmlspecialchars(get_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
<title>OfferAI — AI-тренажёр собеседований</title>
<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='14' fill='%23F8F6F2'/%3E%3Ctext x='50%25' y='54%25' dominant-baseline='middle' text-anchor='middle' font-size='34' fill='%23C4553A' font-family='Arial'%3EO%3C/text%3E%3C/svg%3E">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/css/style.css?v=20260328-v2">
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
<style>
.resume-upload-shell{margin:12px 24px 0}
.file-upload-area{padding:18px;border:1px dashed rgba(196,85,58,.22);border-radius:var(--radius);background:rgba(255,255,255,.72);display:flex;flex-direction:column;align-items:center;gap:6px;text-align:center;cursor:pointer;transition:border-color .2s ease,background .2s ease,transform .2s ease,box-shadow .2s ease}
.file-upload-area:hover,.file-upload-area.dragging{border-color:rgba(196,85,58,.38);background:rgba(196,85,58,.07);transform:translateY(-1px);box-shadow:0 12px 28px rgba(22,34,52,.05)}
.file-upload-icon{font-size:1.35rem;line-height:1}
.file-upload-title{font-size:.94rem;font-weight:600;color:var(--text)}
.file-upload-subtitle{font-size:.82rem;color:var(--text2)}
.file-upload-progress{display:none;margin-top:10px}
.file-upload-progress.show{display:block}
.file-upload-progress-bar{height:4px;border-radius:999px;background:rgba(22,34,52,.10);overflow:hidden}
.file-upload-progress-fill{height:100%;width:0;background:linear-gradient(90deg,linear-gradient(135deg,#C4553A,#D4795F));transition:width 1s ease}
.file-upload-status{margin-top:8px;font-size:.78rem;color:var(--text3)}
.file-success{display:none;margin-top:10px;padding:12px 14px;border:1px solid rgba(47,157,116,.28);border-radius:var(--radius);background:rgba(47,157,116,.10);font-size:.82rem;color:var(--green)}
.file-success.show{display:block}
.saved-session-block{margin:12px 24px 16px;border:1px solid rgba(22,34,52,.08);border-radius:var(--radius);background:rgba(255,255,255,.72);overflow:hidden}
.saved-session-toggle{width:100%;display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 16px;background:transparent;border:0;color:var(--text);font:inherit;font-size:.92rem;font-weight:600;cursor:pointer;text-align:left}
.saved-session-toggle:hover{background:rgba(22,34,52,.04)}
.saved-session-toggle:focus-visible{outline:2px solid rgba(196,85,58,.3);outline-offset:-2px}
.saved-session-arrow{color:var(--text2);font-size:.95rem;line-height:1;transition:transform .2s ease}
.saved-session-block.open .saved-session-arrow{transform:rotate(180deg)}
.saved-session-body{padding:4px 0 6px;border-top:1px solid rgba(22,34,52,.08);background:rgba(22,34,52,.03);max-height:min(56vh,720px);overflow:auto;overscroll-behavior:contain}
.saved-session-body[hidden]{display:none}
.saved-session-body .msg:first-child{margin-top:12px}
.otp-boxes{display:flex;gap:10px;justify-content:center;margin:20px 0}
.otp-box{width:48px;height:56px;border-radius:12px;border:2px solid rgba(22,34,52,.12);background:var(--bg3);font-size:1.4rem;font-weight:700;text-align:center;color:var(--text);outline:none;transition:border-color .2s,transform .1s;caret-color:transparent}
.otp-box:focus{border-color:var(--accent);background:rgba(33,77,198,.06)}
.otp-box.filled{border-color:rgba(33,77,198,.32)}
.otp-box.pop{animation:otpPop .15s ease}
@keyframes otpPop{
  0%{transform:scale(1)}
  50%{transform:scale(1.15)}
  100%{transform:scale(1)}
}
.upgrade-overlay-card{max-width:1080px;text-align:left;max-height:min(90vh,860px);overflow-y:auto}
.upgrade-overlay-card h2{text-align:center}
.upgrade-overlay-card > p{text-align:center;max-width:760px;margin:0 auto 24px}
.upgrade-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;align-items:start}
.upgrade-grid .price-card{padding:24px}
.upgrade-session-explainer{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin:0 0 20px;padding:16px 18px;border-radius:16px;background:rgba(255,255,255,.74);border:1px solid rgba(22,34,52,.08)}
.upgrade-session-explainer strong{display:block;font-size:.95rem}
.upgrade-session-explainer span{display:block;margin-top:6px;font-size:.82rem;color:var(--text2);line-height:1.45}
.upgrade-chip-list{display:flex;flex-wrap:wrap;justify-content:flex-end;gap:8px}
.upgrade-chip-list span{display:inline-flex;align-items:center;padding:6px 10px;border-radius:999px;background:rgba(196,85,58,.07);font-size:.78rem;color:var(--text2)}
.upgrade-summary{font-size:.9rem;color:var(--text2);margin:12px 0 16px;line-height:1.55}
.upgrade-target{font-size:.9rem;color:var(--text2);margin:0 0 16px;line-height:1.5}
.upgrade-outcome{margin-top:16px;padding-top:16px;border-top:1px solid rgba(22,34,52,.08)}
.upgrade-outcome strong{display:block;font-size:.9rem}
.upgrade-outcome p{margin-top:8px;font-size:.82rem;color:var(--text2);line-height:1.5}
.upgrade-footnote{margin-top:18px;font-size:.78rem;color:var(--text3);text-align:center;line-height:1.5}
.channels-page{padding:32px;max-width:900px;margin:0 auto;width:100%}
.channels-header{margin-bottom:32px}
.channels-header h2{font-size:1.6rem;font-weight:700;margin-bottom:8px}
.channels-header p{color:var(--text2);font-size:.95rem}
.channels-filters{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:32px}
.channels-filter-btn{padding:6px 16px;border-radius:20px;border:1px solid rgba(22,34,52,.10);background:transparent;color:var(--text2);font-size:.85rem;cursor:pointer;transition:all .2s}
.channels-filter-btn.active{background:rgba(33,77,198,.10);border-color:rgba(196,85,58,.18);color:var(--accent)}
.channels-category{margin-bottom:40px}
.channels-cat-title{font-size:1rem;font-weight:600;margin-bottom:16px;display:flex;align-items:center;gap:8px;color:var(--text)}
.channels-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:14px}
.channel-card{display:block;padding:16px;border-radius:var(--radius);background:var(--bg3);border:1px solid rgba(22,34,52,.08);text-decoration:none;transition:all .2s;cursor:pointer}
.channel-card:hover{border-color:rgba(196,85,58,.18);transform:translateY(-2px);box-shadow:var(--shadow)}
.channel-card-name{font-size:.95rem;font-weight:600;color:var(--text);margin-bottom:6px;display:flex;align-items:center;gap:8px}
.channel-card-name::before{content:'✈️';font-size:.8rem;opacity:.5}
.channel-card-desc{font-size:.82rem;color:var(--text2);line-height:1.5;margin-bottom:10px}
.channel-card-members{font-size:.75rem;color:var(--text3);display:flex;align-items:center;gap:4px}
.channel-card-badge{display:inline-block;padding:2px 8px;background:rgba(22,34,52,.04);border-radius:4px;font-size:.7rem;color:var(--text3)}
.channels-empty{grid-column:1 / -1;padding:24px;text-align:center;color:var(--text3);font-size:.85rem;border:1px dashed rgba(22,34,52,.10);border-radius:var(--radius);font-style:italic}
@media (max-width:600px){
  .upgrade-overlay-card{max-height:calc(100vh - 32px);padding:24px 18px}
  .upgrade-grid{grid-template-columns:1fr}
  .upgrade-session-explainer{flex-direction:column}
  .upgrade-chip-list{justify-content:flex-start}
  .channels-filters{flex-wrap:nowrap;overflow-x:auto}
}
@media (max-width:640px){
  .otp-boxes{gap:8px}
  .otp-box{width:44px;height:52px}
}
@media (max-width:640px){.resume-upload-shell,.saved-session-block{margin:12px 16px 0}}
</style>
</head>
<body class="app-page">

<!-- ====== AUTH MODAL ====== -->
<div class="auth-modal" id="authModal">
  <div class="auth-card">
    <h2 id="authTitle">Вход</h2>
    <p class="auth-sub" id="authSub">Войдите в личный кабинет</p>
    <div id="authNameField" class="auth-field" style="display:none">
      <label>Имя</label>
      <input type="text" id="authName" placeholder="Как вас зовут">
    </div>
    <div class="auth-field" id="authEmailField">
      <label>Email</label>
      <input type="email" id="authEmail" placeholder="you@example.com">
    </div>
    <div class="auth-field" id="authPassField">
      <label>Пароль</label>
      <input type="password" id="authPass" placeholder="Минимум 6 символов" onkeydown="if(event.key==='Enter')doAuth()">
    </div>
    <div id="authTurnstileField" class="auth-field" style="display:none">
      <div class="cf-turnstile" data-sitekey="<?= htmlspecialchars(TURNSTILE_SITE_KEY, ENT_QUOTES, 'UTF-8') ?>" data-theme="light"></div>
    </div>
    <div id="authCodeField" class="auth-field" style="display:none">
      <label>Код из письма</label>
      <input type="hidden" id="authCode" autocomplete="one-time-code">
      <div class="otp-boxes" id="authOtpBoxes" aria-label="Введите 6-значный код подтверждения">
        <input type="text" class="otp-box" inputmode="numeric" maxlength="1" pattern="[0-9]" autocomplete="one-time-code" aria-label="Первая цифра кода">
        <input type="text" class="otp-box" inputmode="numeric" maxlength="1" pattern="[0-9]" autocomplete="off" aria-label="Вторая цифра кода">
        <input type="text" class="otp-box" inputmode="numeric" maxlength="1" pattern="[0-9]" autocomplete="off" aria-label="Третья цифра кода">
        <input type="text" class="otp-box" inputmode="numeric" maxlength="1" pattern="[0-9]" autocomplete="off" aria-label="Четвертая цифра кода">
        <input type="text" class="otp-box" inputmode="numeric" maxlength="1" pattern="[0-9]" autocomplete="off" aria-label="Пятая цифра кода">
        <input type="text" class="otp-box" inputmode="numeric" maxlength="1" pattern="[0-9]" autocomplete="off" aria-label="Шестая цифра кода">
      </div>
    </div>
    <div id="authInfo" style="display:none;margin:12px 0;padding:12px 14px;border-radius:14px;font-size:.9rem;line-height:1.5"></div>
    <div class="auth-error" id="authError"></div>
    <button class="auth-submit" onclick="doAuth()" id="authSubmit">Войти</button>
    <div id="authMeta" style="display:none;margin-top:10px;font-size:.85rem;text-align:right">
      <a onclick="showAuth('forgot_password')">Забыли пароль?</a>
    </div>
    <div class="auth-switch" id="authSwitch">
      Нет аккаунта? <a onclick="showAuth('register')">Зарегистрироваться</a>
    </div>
    <button style="position:absolute;top:16px;right:16px;background:none;color:var(--text3);font-size:1.3rem;cursor:pointer;border:none" onclick="handleAuthClose()">&times;</button>
  </div>
</div>

<!-- ====== APP ====== -->
<div id="app" style="display:none">
  <div class="app-layout">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
      <div class="sb-header">
        <div class="sb-logo">Offer<span>AI</span></div>
        <button class="sb-close" onclick="document.getElementById('sidebar').classList.remove('open')">&times;</button>
      </div>
      <div class="sb-profile" id="sbProfile">
        <div class="sb-profile-name" id="sbName">—</div>
        <div class="sb-profile-role" id="sbRole">Профиль не создан</div>
        <div class="sb-profile-plan plan-none" id="sbPlan">Пробный</div>
      </div>
      <div class="sb-nav" id="sbNav"></div>
      <div class="sb-footer">
        <div class="sb-uses">Осталось сессий: <strong id="sbUses">—</strong></div>
        <button class="sb-logout" onclick="doLogout()">Выйти</button>
      </div>
    </aside>

    <!-- Main -->
    <div class="main-area">
      <div class="main-header">
        <button class="menu-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')">&#9776;</button>
        <div class="main-title" id="mainTitle">Главная</div>
        <div class="main-subtitle" id="mainSub"></div>
      </div>
      <div class="chat-area" id="chatArea"></div>
      <div class="input-area" id="inputArea" style="display:none">
        <div class="input-row">
          <div class="input-wrap">
            <textarea id="userInput" rows="1" placeholder="Введите сообщение..." oninput="autoGrow(this)" onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendMsg()}"></textarea>
            <button class="voice-btn" id="voiceBtn" onclick="toggleVoice()" title="Голосовой ввод">🎤</button>
          </div>
          <button class="send-btn" onclick="sendMsg()" id="sendBtn">&#10148;</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ====== UPGRADE OVERLAY ====== -->
<div class="overlay" id="upgradeOverlay">
  <div class="overlay-card upgrade-overlay-card" style="position:relative">
    <button class="overlay-close" onclick="hideOverlay()">&times;</button>
    <h2 id="upgradeTitle">Сессии закончились</h2>
    <p id="upgradeDesc">Докупите следующий тариф чтобы продолжить</p>
    <div id="upgradeContent"></div>
  </div>
</div>

<script>
// ====== CONFIG ======
const API_BASE = '/api';
const DEFAULT_AUTH_MODE = 'login';

const PLANS = {
  start: { name:'Старт', price:490, uses:8, modules:['kickoff','decode','resume','pitch','practice'], maxDrill:2, practiceRounds:3 },
  prep:  { name:'Подготовка', price:990, uses:25, modules:['kickoff','decode','resume','pitch','practice','mock','prep','concerns','stories','hype'], maxDrill:8, practiceRounds:8 },
  offer: { name:'Оффер', price:1990, uses:40, modules:['kickoff','decode','resume','pitch','practice','mock','prep','concerns','stories','hype','salary','negotiate'], maxDrill:8, practiceRounds:999 }
};

const MODULES = [
  { id:'kickoff', icon:'🚀', name:'Знакомство', desc:'С чего начать', section:'base', allPlans:true },
  { id:'decode', icon:'🔍', name:'Разбор вакансии', desc:'Анализ вакансии', section:'base', allPlans:true },
  { id:'resume', icon:'📄', name:'Резюме', desc:'Улучшение резюме', section:'base', allPlans:true },
  { id:'pitch', icon:'🎙', name:'Самопрезентация', desc:'Рассказ о себе', section:'base', allPlans:true },
  { id:'practice', icon:'💪', name:'Тренировка', desc:'Уровни 1-8', section:'train', allPlans:true },
  { id:'mock', icon:'🎤', name:'Пробное интервью', desc:'Итоговая оценка', section:'train', minPlan:'prep' },
  { id:'prep', icon:'📋', name:'Подготовка к собеседованию', desc:'Вероятные вопросы', section:'train', minPlan:'prep' },
  { id:'concerns', icon:'⚠️', name:'Возражения рекрутера', desc:'Ответы на возражения', section:'train', minPlan:'prep' },
  { id:'stories', icon:'📚', name:'Мои истории', desc:'Истории из опыта', section:'train', minPlan:'prep' },
  { id:'hype', icon:'🔥', name:'Настрой перед собесом', desc:'Спокойствие и уверенность', section:'train', minPlan:'prep' },
  { id:'salary', icon:'💰', name:'Зарплатные ожидания', desc:'Как называть сумму', section:'offer', minPlan:'offer' },
  { id:'negotiate', icon:'🤝', name:'Переговоры по офферу', desc:'Переговоры', section:'offer', minPlan:'offer' },
];

const DRILL_NAMES = ['','Разминка','Под давлением','Смена темы','Пробелы в опыте','Ролевые вопросы','Панельное интервью','Стресс-интервью','Технические вопросы'];
const SCORE_DIMENSIONS = [
  { display:'Содержание', labels:['Содержание','Substance'] },
  { display:'Структура', labels:['Структура','Structure'] },
  { display:'Попадание в тему', labels:['Попадание в тему','Relevance'] },
  { display:'Убедительность', labels:['Убедительность','Credibility'] },
  { display:'Уникальность', labels:['Уникальность','Differentiation'] },
];
const HIRE_SIGNAL_LABELS = {
  'Strong Hire':'Однозначно берём',
  'Hire':'Берём',
  'Mixed':'Сомнения',
  'No Hire':'Не берём',
  'Однозначно берём':'Однозначно берём',
  'Берём':'Берём',
  'Сомнения':'Сомнения',
  'Не берём':'Не берём'
};
const DIALOG_STATES = {
  'DECODE_DONE':'decode',
  'RESUME_DONE':'resume_standard',
  'PITCH_DONE':'pitch',
  'PREP_DONE':'prep',
  'CONCERNS_DONE':'concerns',
  'SALARY_DONE':'salary',
  'NEGOTIATE_DONE':'negotiate',
};
const CHANNELS_DATA = [
  {
    category:'IT и разработка',
    icon:'💻',
    channels:[
      {
        name:'Работа в IT',
        desc:'Вакансии для разработчиков, аналитиков и продактов',
        url:'https://t.me/rabota_it',
        members:'125 000+'
      },
    ]
  },
  {
    category:'Продукт и аналитика',
    icon:'📊',
    channels:[
      {
        name:'Product Jobs',
        desc:'Вакансии для продакт-менеджеров и аналитиков',
        url:'https://t.me/productjobs',
        members:'скоро'
      },
    ]
  },
  {
    category:'Менеджмент',
    icon:'👔',
    channels:[
      {
        name:'Менеджмент вакансии',
        desc:'Руководящие позиции и управленческие роли',
        url:'https://t.me/management_jobs',
        members:'скоро'
      },
    ]
  },
  {
    category:'Маркетинг',
    icon:'📣',
    channels:[
      {
        name:'Маркетинг работа',
        desc:'Вакансии для маркетологов и специалистов по рекламе',
        url:'https://t.me/marketing_jobs',
        members:'скоро'
      },
    ]
  },
  {
    category:'Все профессии',
    icon:'🌐',
    channels:[
      {
        name:'Работа в России',
        desc:'Вакансии для всех специальностей по всей стране',
        url:'https://t.me/rabota_ru',
        members:'скоро'
      },
    ]
  },
];

// ====== STATE ======
let currentUser = null;
let CS = null;
let appState = 'HOME';
let chatHistory = [];
let dialogFollowupCount = 0;
const DIALOG_MAX_FOLLOWUPS = 3;
let practiceRound = 0;
let practiceQuestion = '';
let practiceAnswer = '';
let practiceQuestions = [];
let practiceConsumed = false;
let mockQuestions = [];
let mockAnswers = [];
let mockIndex = 0;
let resumeDepth = 'standard';
let isProcessing = false;
let stateSyncTimer = null;
let voiceTimer = null;
let chatLogSaveTimer = null;
const MAX_ARCHIVED_SESSIONS_PER_MODULE = 3;

function defaultCS(){
  return {
    profile:{role:'',track:'',dir:'',tl:'',hist:'',cv:'',lastModule:''},
    chatLogs:{},
    chatLogHistory:{},
    chatLogStates:{},
    chatLogTimestamps:{},
    chatDrafts:{},
    dialogFollowups:{},
    dialogModules:{},
    moduleInputs:{},
    storybank:[],
    scoreHistory:[],
    drillStage:1,
    sessionCount:0,
    lastJD:'',
    practiceInProgress:null,
    mockInProgress:null
  };
}

function normalizeState(state){
  const base = defaultCS();
  const next = state && typeof state === 'object' ? state : {};
  const rawPractice = next.practiceInProgress && typeof next.practiceInProgress === 'object' ? next.practiceInProgress : null;
  const rawMock = next.mockInProgress && typeof next.mockInProgress === 'object' ? next.mockInProgress : null;
  const normalizedHistory = {};
  if(next.chatLogHistory && typeof next.chatLogHistory === 'object' && !Array.isArray(next.chatLogHistory)){
    Object.entries(next.chatLogHistory).forEach(([moduleId, entries]) => {
      if(!Array.isArray(entries)) return;
      const cleanEntries = entries
        .map(entry => {
          if(!entry || typeof entry !== 'object') return null;
          const html = typeof entry.html === 'string' ? entry.html : '';
          if(!html.trim()) return null;
          return {
            html,
            state:typeof entry.state === 'string' ? entry.state : '',
            ts:Number.isFinite(Number(entry.ts)) ? Number(entry.ts) : Date.now()
          };
        })
        .filter(Boolean)
        .slice(0, MAX_ARCHIVED_SESSIONS_PER_MODULE);
      if(cleanEntries.length){
        normalizedHistory[moduleId] = cleanEntries;
      }
    });
  }
  return {
    ...base,
    ...next,
    profile:{...base.profile,...(next.profile||{})},
    chatLogs:(typeof next.chatLogs === 'object' &&
      next.chatLogs !== null &&
      !Array.isArray(next.chatLogs))
      ? next.chatLogs : {},
    chatLogHistory:normalizedHistory,
    chatLogStates:(typeof next.chatLogStates === 'object' &&
      next.chatLogStates !== null &&
      !Array.isArray(next.chatLogStates))
      ? next.chatLogStates : {},
    chatLogTimestamps:(typeof next.chatLogTimestamps === 'object' &&
      next.chatLogTimestamps !== null &&
      !Array.isArray(next.chatLogTimestamps))
      ? next.chatLogTimestamps : {},
    chatDrafts:(typeof next.chatDrafts === 'object' &&
      next.chatDrafts !== null &&
      !Array.isArray(next.chatDrafts))
      ? next.chatDrafts : {},
    dialogFollowups:(typeof next.dialogFollowups === 'object' &&
      next.dialogFollowups !== null &&
      !Array.isArray(next.dialogFollowups))
      ? next.dialogFollowups : {},
    dialogModules:(typeof next.dialogModules === 'object' &&
      next.dialogModules !== null &&
      !Array.isArray(next.dialogModules))
      ? next.dialogModules : {},
    moduleInputs:(typeof next.moduleInputs === 'object' &&
      next.moduleInputs !== null &&
      !Array.isArray(next.moduleInputs))
      ? next.moduleInputs : {},
    storybank:Array.isArray(next.storybank)?next.storybank:[],
    scoreHistory:Array.isArray(next.scoreHistory)?next.scoreHistory:[],
    drillStage:Number.isFinite(next.drillStage) && next.drillStage > 0 ? next.drillStage : 1,
    sessionCount:Number.isFinite(next.sessionCount) && next.sessionCount >= 0 ? next.sessionCount : 0,
    lastJD:typeof next.lastJD === 'string' ? next.lastJD : '',
    practiceInProgress:rawPractice ? {
      round:Number.isFinite(rawPractice.round) && rawPractice.round >= 0 ? rawPractice.round : 0,
      question:typeof rawPractice.question === 'string' ? rawPractice.question : '',
      answer:typeof rawPractice.answer === 'string' ? rawPractice.answer : '',
      questions:Array.isArray(rawPractice.questions) ? rawPractice.questions.map(item => String(item || '')) : [],
      consumed:Boolean(rawPractice.consumed),
      state:typeof rawPractice.state === 'string' ? rawPractice.state : ''
    } : null,
    mockInProgress:rawMock ? {
      questions:Array.isArray(rawMock.questions) ? rawMock.questions : [],
      answers:Array.isArray(rawMock.answers) ? rawMock.answers : [],
      index:Number.isFinite(rawMock.index) && rawMock.index >= 0 ? rawMock.index : 0,
      billed:Boolean(rawMock.billed)
    } : null
  };
}

function stateCacheKey(user = currentUser){
  return user ? `offerai_cs_cache_${user.id}` : null;
}

function persistCSCache(){
  const key = stateCacheKey();
  if(!key || !CS) return;
  localStorage.setItem(key, JSON.stringify(CS));
}

function syncCurrentUser(user){
  if(!user){
    currentUser = null;
    return;
  }
  currentUser = {
    ...user,
    usesLeft:Number.isFinite(Number(user.usesLeft)) ? Number(user.usesLeft) : 0
  };
}

async function requestJSON(url, options = {}){
  const method = (options.method || 'GET').toUpperCase();
  const headers = {
    'Accept':'application/json',
    ...(options.body ? {'Content-Type':'application/json'} : {}),
    ...(options.headers || {})
  };
  if(method !== 'GET'){
    headers['X-CSRF-Token'] = getCsrfToken();
  }
  const init = {
    credentials:'include',
    ...options,
    headers,
    method
  };
  const res = await fetch(url, init);
  let data = {};
  try{
    data = await res.json();
  }catch(_err){
    data = {};
  }
  if(!res.ok){
    const err = new Error(data.error || 'Ошибка запроса');
    err.status = res.status;
    err.data = data;
    throw err;
  }
  return data;
}

function getCsrfToken(){
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

function queueStateSync(){
  if(!currentUser || !CS) return;
  clearTimeout(stateSyncTimer);
  stateSyncTimer = setTimeout(async()=>{
    try{
      await requestJSON(`${API_BASE}/state.php`,{
        method:'POST',
        body:JSON.stringify({state:CS})
      });
    }catch(err){
      console.warn('State sync failed', err);
    }
  }, 150);
}

async function refreshCurrentUser(){
  const data = await requestJSON(`${API_BASE}/auth.php?action=check`);
  syncCurrentUser(data.user);
  return currentUser;
}

// ====== AUTH (server-backed) ======
let pendingVerificationEmail = '';
let authOtpLastAutoSubmittedCode = '';

function isValidEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
  if (!re.test(email)) return false;

  const domain = email.split('@')[1];
  if (!domain || !domain.includes('.')) return false;

  return true;
}

function showAuthInfo(msg){
  const info=document.getElementById('authInfo');
  const error=document.getElementById('authError');
  error.style.display='none';
  if(!msg){
    info.style.display='none';
    info.textContent='';
    return;
  }
  info.textContent=msg;
  info.style.display='block';
}

function getAuthOtpBoxes(){
  return Array.from(document.querySelectorAll('#authOtpBoxes .otp-box'));
}

function setAuthOtpBoxValue(box, value){
  box.value = value;
  box.classList.toggle('filled', Boolean(value));
}

function syncAuthCodeFromOtp(){
  const code = getAuthOtpBoxes().map(box => box.value).join('');
  const hiddenInput = document.getElementById('authCode');
  if(hiddenInput){
    hiddenInput.value = code;
  }
  if(code.length < 6){
    authOtpLastAutoSubmittedCode = '';
  }
  return code;
}

function focusAuthOtpBox(index){
  const boxes = getAuthOtpBoxes();
  if(!boxes.length) return;
  const safeIndex = Math.max(0, Math.min(index, boxes.length - 1));
  boxes[safeIndex].focus();
  boxes[safeIndex].select();
}

function firstEmptyAuthOtpIndex(){
  const boxes = getAuthOtpBoxes();
  const index = boxes.findIndex(box => !box.value);
  return index === -1 ? boxes.length - 1 : index;
}

function focusFirstEmptyAuthOtp(){
  const boxes = getAuthOtpBoxes();
  if(!boxes.length) return;
  focusAuthOtpBox(firstEmptyAuthOtpIndex());
}

function resetAuthOtp(shouldFocus = false){
  const hiddenInput = document.getElementById('authCode');
  if(hiddenInput){
    hiddenInput.value = '';
  }
  authOtpLastAutoSubmittedCode = '';
  getAuthOtpBoxes().forEach(box => {
    box.value = '';
    box.classList.remove('filled', 'pop');
  });
  if(shouldFocus){
    window.setTimeout(() => focusFirstEmptyAuthOtp(), 0);
  }
}

function triggerAuthOtpPop(box){
  box.classList.remove('pop');
  void box.offsetWidth;
  box.classList.add('pop');
  window.setTimeout(() => box.classList.remove('pop'), 150);
}

function maybeAutoSubmitAuthCode(code){
  const modal = document.getElementById('authModal');
  if(modal?.dataset.mode !== 'verify_email' || code.length !== 6) return;
  if(authOtpLastAutoSubmittedCode === code) return;
  authOtpLastAutoSubmittedCode = code;
  window.setTimeout(() => {
    if(document.getElementById('authModal')?.dataset.mode !== 'verify_email') return;
    if(document.getElementById('authCode').value.trim().length !== 6) return;
    document.getElementById('authSubmit').click();
  }, 0);
}

function handleAuthOtpInput(event){
  const box = event.target;
  const boxes = getAuthOtpBoxes();
  const index = boxes.indexOf(box);
  if(index === -1) return;

  const digit = box.value.replace(/\D/g, '').slice(-1);
  setAuthOtpBoxValue(box, digit);

  if(digit){
    triggerAuthOtpPop(box);
    if(index < boxes.length - 1){
      focusAuthOtpBox(index + 1);
    }
  }

  const code = syncAuthCodeFromOtp();
  if(code.length === boxes.length){
    maybeAutoSubmitAuthCode(code);
  }
}

function handleAuthOtpKeydown(event){
  const box = event.target;
  const boxes = getAuthOtpBoxes();
  const index = boxes.indexOf(box);
  if(index === -1) return;

  if(event.key === 'Backspace'){
    event.preventDefault();
    if(box.value){
      setAuthOtpBoxValue(box, '');
      syncAuthCodeFromOtp();
      if(index > 0){
        focusAuthOtpBox(index - 1);
      }
      return;
    }
    if(index > 0){
      const prevBox = boxes[index - 1];
      setAuthOtpBoxValue(prevBox, '');
      syncAuthCodeFromOtp();
      focusAuthOtpBox(index - 1);
    }
    return;
  }

  if(event.key === 'ArrowLeft' && index > 0){
    event.preventDefault();
    focusAuthOtpBox(index - 1);
    return;
  }

  if(event.key === 'ArrowRight' && index < boxes.length - 1){
    event.preventDefault();
    focusAuthOtpBox(index + 1);
    return;
  }

  if(event.key === 'Enter'){
    const code = syncAuthCodeFromOtp();
    if(code.length === boxes.length){
      event.preventDefault();
      document.getElementById('authSubmit').click();
    }
    return;
  }

  if(event.key.length === 1 && !/[0-9]/.test(event.key)){
    event.preventDefault();
  }
}

function handleAuthOtpPaste(event){
  const digits = (event.clipboardData?.getData('text') || '').replace(/\D/g, '').slice(0, 6);
  if(!digits) return;

  event.preventDefault();
  const boxes = getAuthOtpBoxes();
  boxes.forEach((box, index) => {
    setAuthOtpBoxValue(box, digits[index] || '');
  });

  const code = syncAuthCodeFromOtp();
  focusAuthOtpBox(Math.max(0, Math.min(digits.length, boxes.length) - 1));
  if(code.length === boxes.length){
    maybeAutoSubmitAuthCode(code);
  }
}

function handleAuthOtpFocus(event){
  const box = event.target;
  if(box.value) return;

  const boxes = getAuthOtpBoxes();
  const firstEmptyIndex = boxes.findIndex(item => !item.value);
  if(firstEmptyIndex === -1) return;

  const firstEmptyBox = boxes[firstEmptyIndex];
  if(firstEmptyBox !== box){
    window.setTimeout(() => firstEmptyBox.focus(), 0);
  }
}

function handleAuthOtpClick(event){
  const box = event.target.closest('.otp-box');
  if(!box || box.value) return;

  const boxes = getAuthOtpBoxes();
  const firstEmptyBox = boxes.find(item => !item.value);
  if(firstEmptyBox && firstEmptyBox !== box){
    event.preventDefault();
    firstEmptyBox.focus();
  }
}

function setupAuthOtpInputs(){
  const container = document.getElementById('authOtpBoxes');
  if(!container || container.dataset.ready === '1') return;

  container.dataset.ready = '1';
  container.addEventListener('click', handleAuthOtpClick);
  getAuthOtpBoxes().forEach(box => {
    box.addEventListener('input', handleAuthOtpInput);
    box.addEventListener('keydown', handleAuthOtpKeydown);
    box.addEventListener('paste', handleAuthOtpPaste);
    box.addEventListener('focus', handleAuthOtpFocus);
  });
}

function showAuth(mode){
  const m = document.getElementById('authModal');
  const title = document.getElementById('authTitle');
  const sub = document.getElementById('authSub');
  const nameField = document.getElementById('authNameField');
  const emailField = document.getElementById('authEmailField');
  const passField = document.getElementById('authPassField');
  const turnstileField = document.getElementById('authTurnstileField');
  const codeField = document.getElementById('authCodeField');
  const submit = document.getElementById('authSubmit');
  const switcher = document.getElementById('authSwitch');
  const meta = document.getElementById('authMeta');
  m.classList.add('show');
  showAuthInfo('');
  document.getElementById('authError').style.display='none';
  resetAuthOtp();
  meta.style.display='none';
  nameField.style.display='none';
  emailField.style.display='none';
  passField.style.display='none';
  turnstileField.style.display='none';
  codeField.style.display='none';

  if(mode==='register'){
    title.textContent='Регистрация';
    sub.textContent='Создайте аккаунт и подтвердите email кодом из письма';
    nameField.style.display='block';
    emailField.style.display='block';
    passField.style.display='block';
    turnstileField.style.display='block';
    submit.textContent='Зарегистрироваться';
    switcher.innerHTML='Уже есть аккаунт? <a onclick="showAuth(\'login\')">Войти</a>';
    m.dataset.mode='register';
    if(window.turnstile) window.turnstile.reset();
    return;
  }

  if(mode==='verify_email'){
    pendingVerificationEmail = (currentUser && currentUser.email) || pendingVerificationEmail || document.getElementById('authEmail').value.trim();
    title.textContent='Подтвердите email';
    sub.textContent = pendingVerificationEmail
      ? `Введите 6-значный код, который мы отправили на ${pendingVerificationEmail}`
      : 'Введите 6-значный код из письма';
    codeField.style.display='block';
    submit.textContent='Подтвердить код';
    switcher.innerHTML='Код не пришёл? <a onclick="resendVerificationCode()">Отправить код повторно</a><br>Нужен другой email? <a onclick="showAuth(\'login\')">Войти заново</a>';
    m.dataset.mode='verify_email';
    focusFirstEmptyAuthOtp();
    return;
  }

  if(mode==='forgot_password'){
    title.textContent='Восстановление пароля';
    sub.textContent='Введите email — мы отправим ссылку для смены пароля';
    emailField.style.display='block';
    submit.textContent='Отправить ссылку';
    switcher.innerHTML='Вспомнили пароль? <a onclick="showAuth(\'login\')">Войти</a>';
    m.dataset.mode='forgot_password';
    return;
  }

  title.textContent='Вход';
  sub.textContent='Войдите в личный кабинет';
  emailField.style.display='block';
  passField.style.display='block';
  submit.textContent='Войти';
  meta.style.display='block';
  switcher.innerHTML='Нет аккаунта? <a onclick="showAuth(\'register\')">Зарегистрироваться</a>';
  m.dataset.mode='login';
}

function showAuthError(msg){
  showAuthInfo('');
  const e=document.getElementById('authError');
  e.textContent=msg; e.style.display='block';
}

function hideAuth(){
  document.getElementById('authModal').classList.remove('show');
}

function handleAuthClose(){
  if(!currentUser || currentUser.emailVerified === false){
    window.location.href='/';
    return;
  }
  hideAuth();
}

async function resendVerificationCode(){
  try{
    const data = await requestJSON(`${API_BASE}/auth.php?action=resend_verification`,{
      method:'POST',
      body:JSON.stringify({})
    });
    if(data.user) syncCurrentUser(data.user);
    pendingVerificationEmail = data.email || pendingVerificationEmail;
    showAuth('verify_email');
    showAuthInfo(data.message || 'Новый код отправлен.');
  }catch(err){
    showAuthError(err.message || 'Не удалось отправить код повторно');
  }
}

async function doAuth(){
  const m=document.getElementById('authModal');
  const mode=m.dataset.mode;
  const email=document.getElementById('authEmail').value.trim();
  const pass=document.getElementById('authPass').value;
  const name=document.getElementById('authName').value.trim();
  const code=document.getElementById('authCode').value.trim();
  const turnstileToken=document.querySelector('[name="cf-turnstile-response"]')?.value || '';

  try{
    if(mode==='verify_email'){
      if(!/^\d{6}$/.test(code)){
        showAuthError('Введите 6 цифр из письма');
        return;
      }
      const data = await requestJSON(`${API_BASE}/auth.php?action=verify_email_code`,{
        method:'POST',
        body:JSON.stringify({code})
      });
      await loginAs(data.user);
      return;
    }

    if(mode==='forgot_password'){
      if(!email){
        showAuthError('Введите email');
        return;
      }
      if(!isValidEmail(email)){
        showAuthError('Введите корректный email-адрес');
        return;
      }
      const data = await requestJSON(`${API_BASE}/auth.php?action=request_password_reset`,{
        method:'POST',
        body:JSON.stringify({email})
      });
      showAuth('forgot_password');
      showAuthInfo(data.message || 'Если такой email существует, мы отправили ссылку для смены пароля.');
      return;
    }

    if(!email||!pass){showAuthError('Заполните все поля');return}
    if(!isValidEmail(email)){showAuthError('Введите корректный email-адрес');return}
    if(pass.length<6){showAuthError('Пароль минимум 6 символов');return}
    if(mode==='register' && !name){showAuthError('Укажите имя');return}
    if(mode==='register' && !turnstileToken){showAuthError('Пройдите проверку');return}

    const payload = mode === 'register'
      ? {name,email,password:pass,'cf-turnstile-response':turnstileToken}
      : {email,password:pass};
    const data = await requestJSON(`${API_BASE}/auth.php?action=${mode}`,{
      method:'POST',
      body:JSON.stringify(payload)
    });

    if(mode==='register' && window.turnstile) window.turnstile.reset();

    if(data.user){
      syncCurrentUser(data.user);
    }

    if(data.status === 'pending_verification'){
      pendingVerificationEmail = data.email || email;
      showAuth('verify_email');
      showAuthInfo(data.message || 'Введите код из письма.');
      return;
    }

    await loginAs(data.user);
  }catch(err){
    showAuthError(err.message || 'Не удалось выполнить авторизацию');
  }
}

async function loginAs(user){
  syncCurrentUser(user);

  if(currentUser && currentUser.emailVerified === false){
    pendingVerificationEmail = currentUser.email || pendingVerificationEmail;
    showAuth('verify_email');
    return;
  }

  hideAuth();
  const paymentSuccess = new URLSearchParams(window.location.search).get('payment') === 'success';
  if(paymentSuccess){
    await refreshCurrentUser();
    clearAppQueryParams();
  }
  await loadCS();
  enterApp();
  if(paymentSuccess){
    updateSidebar();
    showHome();
  }
}

async function loadCS(){
  const key = stateCacheKey();
  const saved = key ? localStorage.getItem(key) : null;

  if(saved){
    try{
      CS = normalizeState(JSON.parse(saved));
    }catch(_err){
      CS = defaultCS();
      persistCSCache();
    }
  } else {
    CS = defaultCS();
  }

  try{
    const data = await requestJSON(`${API_BASE}/state.php`);
    if(data.state){
      CS = normalizeState(data.state);
      persistCSCache();
    } else if(saved){
      queueStateSync();
    } else {
      persistCSCache();
      queueStateSync();
    }
  }catch(err){
    console.warn('State load failed', err);
    if(!CS){
      CS = defaultCS();
    }
    persistCSCache();
  }
}

function saveCS(){
  if(!currentUser) return;
  persistCSCache();
  queueStateSync();
}

function saveChatDraft(moduleId){
  if(!moduleId || !CS || appState === 'HOME') return;
  const input = document.getElementById('userInput');
  if(!input) return;

  const value = trimInput(input.value.trim(), 5000, 'chat_draft');
  if(value){
    CS.chatDrafts[moduleId] = value;
  } else {
    delete CS.chatDrafts[moduleId];
  }
}

function restoreChatDraft(moduleId){
  if(!moduleId || !CS || !CS.chatDrafts) return;
  const input = document.getElementById('userInput');
  const draft = CS.chatDrafts[moduleId];
  if(!input || !draft) return;

  input.value = draft;
  autoGrow(input);
}

function cleanupChatLog(root){
  if(!root) return;

  root.querySelectorAll('.msg-buttons, .chat-log-banner, .saved-session-block').forEach(el => el.remove());
  root.querySelectorAll('#typingIndicator').forEach(el => el.remove());
  const uploadShells = Array.from(root.querySelectorAll('.resume-upload-shell'));
  uploadShells.slice(0, -1).forEach(el => el.remove());

  root.querySelectorAll('.msg').forEach(el => {
    const bubble = el.querySelector('.msg-bubble');
    if(!bubble && !el.textContent.trim()){
      el.remove();
    }
  });

  let prevAiBubble = '';
  Array.from(root.children).forEach(node => {
    if(!(node instanceof HTMLElement)){
      prevAiBubble = '';
      return;
    }
    if(!node.classList.contains('msg') || !node.classList.contains('ai')){
      prevAiBubble = '';
      return;
    }

    const bubble = node.querySelector('.msg-bubble');
    const html = bubble ? bubble.innerHTML.trim() : '';
    if(!html){
      node.remove();
      return;
    }
    if(html === prevAiBubble){
      node.remove();
      return;
    }
    prevAiBubble = html;
  });
}

function isPersistableModule(moduleId){
  return Boolean(moduleId && MODULES.some(module => module.id === moduleId));
}

function shouldPersistCurrentView(moduleId){
  return Boolean(CS && isPersistableModule(moduleId) && appState !== 'HOME' && appState !== 'CHANNELS');
}

function sanitizeStoredSessionHtml(rawHtml){
  if(!rawHtml) return '';
  const temp = document.createElement('div');
  temp.innerHTML = String(rawHtml);
  cleanupChatLog(temp);

  if(temp.querySelector('.channels-page, .home-screen')){
    return '';
  }

  if(!temp.querySelector('.msg, .resume-upload-shell')){
    return '';
  }

  return temp.innerHTML.trim();
}

function getModuleHistoryEntries(moduleId){
  if(!moduleId || !CS) return [];

  const entries = [];
  const seen = new Set();
  let mutated = false;

  const currentHtml = sanitizeStoredSessionHtml(CS.chatLogs?.[moduleId] || '');
  const currentState = String(CS.chatLogStates?.[moduleId] || '');
  const currentTs = Number(CS.chatLogTimestamps?.[moduleId]);

  if(CS.chatLogs?.[moduleId] && !currentHtml){
    clearStoredChatLog(moduleId);
    mutated = true;
  } else if(currentHtml){
    const key = `${currentState}::${currentHtml}`;
    seen.add(key);
    entries.push({
      html:currentHtml,
      state:currentState,
      ts:Number.isFinite(currentTs) && currentTs > 0 ? currentTs : Date.now(),
      kind:'current'
    });
  }

  const rawHistory = Array.isArray(CS.chatLogHistory?.[moduleId]) ? CS.chatLogHistory[moduleId] : [];
  const cleanedHistory = [];

  rawHistory.forEach(item => {
    if(!item || typeof item !== 'object') return;
    const html = sanitizeStoredSessionHtml(item.html || '');
    if(!html) {
      mutated = true;
      return;
    }
    const state = String(item.state || '');
    const ts = Number.isFinite(Number(item.ts)) && Number(item.ts) > 0 ? Number(item.ts) : Date.now();
    const key = `${state}::${html}`;
    if(seen.has(key)){
      mutated = true;
      return;
    }
    seen.add(key);
    cleanedHistory.push({html, state, ts});
  });

  const trimmedHistory = cleanedHistory.slice(0, MAX_ARCHIVED_SESSIONS_PER_MODULE);
  if(cleanedHistory.length !== trimmedHistory.length){
    mutated = true;
  }

  if(trimmedHistory.length){
    CS.chatLogHistory[moduleId] = trimmedHistory;
    trimmedHistory.forEach(entry => entries.push({...entry, kind:'archived'}));
  } else if(CS.chatLogHistory?.[moduleId]){
    delete CS.chatLogHistory[moduleId];
    mutated = true;
  }

  if(mutated){
    saveCS();
  }

  return entries;
}

function formatSessionLabel(entry){
  const tsLabel = Number.isFinite(Number(entry?.ts)) && Number(entry.ts) > 0
    ? new Date(Number(entry.ts)).toLocaleString('ru-RU', {
        day:'2-digit',
        month:'2-digit',
        hour:'2-digit',
        minute:'2-digit'
      })
    : '';
  const suffix = tsLabel ? ` · ${tsLabel}` : '';
  if(entry?.kind === 'current'){
    return String(entry?.state || '').endsWith('_DONE')
      ? `Последняя сессия${suffix}`
      : `Незавершённая сессия${suffix}`;
  }
  return `Сессия${suffix}`;
}

function createSavedSessionBlock(title, savedHtml, kind = 'archived'){
  const wrapper = document.createElement('section');
  wrapper.className = 'saved-session-block';
  wrapper.dataset.sessionKind = kind;
  wrapper.innerHTML = `
    <button type="button" class="saved-session-toggle" aria-expanded="false">
      <span class="saved-session-title"></span>
      <span class="saved-session-arrow">↓</span>
    </button>
    <div class="saved-session-body" hidden></div>
  `;

  const body = wrapper.querySelector('.saved-session-body');
  const label = wrapper.querySelector('.saved-session-title');
  const toggle = wrapper.querySelector('.saved-session-toggle');
  const arrow = wrapper.querySelector('.saved-session-arrow');
  if(!body || !label || !toggle || !arrow) return wrapper;

  label.textContent = title;
  body.innerHTML = savedHtml;
  toggle.addEventListener('click', () => {
    const isOpen = wrapper.classList.toggle('open');
    body.hidden = !isOpen;
    toggle.setAttribute('aria-expanded', String(isOpen));
    arrow.textContent = isOpen ? '↑' : '↓';
  });

  return wrapper;
}

function archiveCurrentChatLog(moduleId){
  if(!moduleId || !CS || !CS.chatLogs?.[moduleId]) return;

  const html = sanitizeStoredSessionHtml(CS.chatLogs[moduleId]);
  if(!html){
    clearStoredChatLog(moduleId);
    return;
  }

  const state = String(CS.chatLogStates?.[moduleId] || '');
  const ts = Number.isFinite(Number(CS.chatLogTimestamps?.[moduleId])) && Number(CS.chatLogTimestamps[moduleId]) > 0
    ? Number(CS.chatLogTimestamps[moduleId])
    : Date.now();
  const history = Array.isArray(CS.chatLogHistory?.[moduleId]) ? [...CS.chatLogHistory[moduleId]] : [];
  const latest = history[0];

  if(!latest || latest.html !== html || String(latest.state || '') !== state){
    history.unshift({html, state, ts});
  }

  CS.chatLogHistory[moduleId] = history.slice(0, MAX_ARCHIVED_SESSIONS_PER_MODULE);
  clearStoredChatLog(moduleId);
}

function mergeSessionHtml(baseHtml, liveHtml){
  if(!baseHtml) return liveHtml;
  if(!liveHtml) return baseHtml;

  const merged = document.createElement('div');
  merged.innerHTML = baseHtml;
  const live = document.createElement('div');
  live.innerHTML = liveHtml;
  Array.from(live.children).forEach(node => {
    merged.appendChild(node.cloneNode(true));
  });
  cleanupChatLog(merged);
  return merged.innerHTML.trim();
}

function saveChatLog(moduleId) {
  if (!moduleId || !CS || !shouldPersistCurrentView(moduleId)) return;
  const chatArea = document.getElementById('chatArea');
  if (!chatArea) return;

  const clone = chatArea.cloneNode(true);
  const currentSessionBody = clone.querySelector('.saved-session-block[data-session-kind="current"] .saved-session-body');
  const baseHtml = currentSessionBody ? sanitizeStoredSessionHtml(currentSessionBody.innerHTML) : '';
  cleanupChatLog(clone);
  const liveHtml = sanitizeStoredSessionHtml(clone.innerHTML);
  const html = mergeSessionHtml(baseHtml, liveHtml);
  if (html) {
    CS.chatLogs[moduleId] = html;
    CS.chatLogStates[moduleId] = appState;
    CS.chatLogTimestamps[moduleId] = Date.now();
  } else {
    clearStoredChatLog(moduleId);
  }
  saveCS();
}

function restoreChatLog(moduleId) {
  if (!moduleId || !CS) return false;
  const chatArea = document.getElementById('chatArea');
  if (!chatArea) return false;

  const entries = getModuleHistoryEntries(moduleId);
  if(!entries.length) return false;

  entries.forEach(entry => {
    chatArea.appendChild(createSavedSessionBlock(formatSessionLabel(entry), entry.html, entry.kind));
  });
  chatArea.scrollTop = chatArea.scrollHeight;

  return true;
}

function savedChatLogState(moduleId){
  if(!moduleId || !CS || !CS.chatLogStates) return '';
  return String(CS.chatLogStates[moduleId] || '');
}

function savedChatLogContains(moduleId, fragment){
  if(!moduleId || !fragment || !CS || !CS.chatLogs) return false;
  return String(CS.chatLogs[moduleId] || '').includes(fragment);
}

function getDialogFollowupCount(moduleId){
  if(!moduleId || !CS || !CS.dialogFollowups) return 0;
  const used = Number(CS.dialogFollowups[moduleId]);
  return Number.isFinite(used) && used >= 0 ? Math.min(used, DIALOG_MAX_FOLLOWUPS) : 0;
}

function getDialogStorageKey(moduleId){
  const value = String(moduleId || '');
  return value.startsWith('resume_') ? 'resume' : value;
}

function setDialogFollowupCount(moduleId, used){
  if(!moduleId || !CS) return;
  const nextUsed = Number.isFinite(used) ? Math.max(0, Math.min(DIALOG_MAX_FOLLOWUPS, used)) : 0;
  CS.dialogFollowups[moduleId] = nextUsed;
  dialogFollowupCount = nextUsed;
  saveCS();
}

function getDialogPlaceholder(moduleId){
  const used = getDialogFollowupCount(moduleId);
  const remaining = Math.max(DIALOG_MAX_FOLLOWUPS - used, 0);
  return `Уточняющий вопрос (осталось ${remaining} из ${DIALOG_MAX_FOLLOWUPS})...`;
}

function setDialogModule(moduleId, aiModule){
  if(!moduleId || !CS) return;
  const value = String(aiModule || '').trim();
  if(value){
    CS.dialogModules[moduleId] = value;
  } else {
    delete CS.dialogModules[moduleId];
  }
  saveCS();
}

function getDialogModule(moduleId, fallback=''){
  if(!moduleId || !CS || !CS.dialogModules) return String(fallback || moduleId || '');
  const saved = String(CS.dialogModules[moduleId] || '').trim();
  return saved || String(fallback || moduleId || '');
}

function rememberModuleInput(moduleId, text){
  if(!moduleId || !CS) return;
  const value = trimInput(String(text || ''), 6000, `${moduleId}.source`);
  if(value){
    CS.moduleInputs[moduleId] = value;
  } else {
    delete CS.moduleInputs[moduleId];
  }
  saveCS();
}

function getModuleInput(moduleId, fallback=''){
  if(!moduleId || !CS || !CS.moduleInputs) return String(fallback || '');
  const saved = String(CS.moduleInputs[moduleId] || '').trim();
  return saved || String(fallback || '');
}

function clearStoredChatLog(moduleId){
  if(!moduleId || !CS) return;
  delete CS.chatLogs[moduleId];
  delete CS.chatLogStates[moduleId];
  delete CS.chatLogTimestamps[moduleId];
}

function queueChatLogSave(moduleId = CS?.profile?.lastModule){
  if(!shouldPersistCurrentView(moduleId)) return;
  clearTimeout(chatLogSaveTimer);
  chatLogSaveTimer = setTimeout(() => saveChatLog(moduleId), 60);
}

function clearModuleSession(moduleId, {archiveCurrent = true} = {}){
  if(!moduleId || !CS) return;
  if(archiveCurrent){
    archiveCurrentChatLog(moduleId);
  } else {
    clearStoredChatLog(moduleId);
  }
  delete CS.chatDrafts[moduleId];
  delete CS.dialogFollowups[moduleId];
  delete CS.dialogModules[moduleId];
  delete CS.moduleInputs[moduleId];
  if(moduleId === 'practice'){
    delete CS.practiceInProgress;
  }
  if(moduleId === 'mock'){
    delete CS.mockInProgress;
  }
  saveCS();
}

function restartModule(moduleId){
  clearModuleSession(moduleId);
  forceStartModule(moduleId);
}

function getCompletionButtons(moduleId){
  switch(moduleId){
    case 'decode':
      return [
        {label:'📄 Оптимизировать резюме под эту вакансию',action:"startModule('resume')"},
        {label:'🏠 На главную',action:"showHome()"},
      ];
    case 'resume':
      return [
        {label:'Разобрать заново',action:"restartModule('resume')"},
        {label:'🎙 Подготовить рассказ о себе',action:"startModule('pitch')"},
        {label:'🏠 На главную',action:"showHome()"},
      ];
    case 'pitch':
      return [
        {label:'Составить рассказ',action:"restartModule('pitch')"},
        {label:'💪 Начать тренировку',action:"startModule('practice')"},
        {label:'🏠 На главную',action:"showHome()"},
      ];
    case 'prep':
      return [
        {label:'💪 Потренироваться на этих вопросах',action:"startModule('practice')"},
        {label:'🏠 На главную',action:'showHome()'},
      ];
    case 'concerns':
      return [
        {label:'Разобрать заново',action:"restartModule('concerns')"},
        {label:'🏠 На главную',action:'showHome()'},
      ];
    case 'salary':
      return [
        {label:'🤝 Переговоры по офферу',action:"startModule('negotiate')"},
        {label:'🏠 На главную',action:'showHome()'},
      ];
    case 'negotiate':
      return [
        {label:'🏠 На главную',action:'showHome()'},
      ];
    default:
      return [];
  }
}

function inputAction(text){
  return `handleInput(${JSON.stringify(String(text))})`;
}

function startModuleAction(id){
  return `startModule(${JSON.stringify(String(id))})`;
}

function setInputValue(text){
  const input = document.getElementById('userInput');
  if(!input) return;
  input.value = text;
  autoGrow(input);
}

function getResumeUploadShell(){
  const shells = Array.from(document.querySelectorAll('.resume-upload-shell'));
  shells.slice(0, -1).forEach(el => el.remove());
  return shells.length ? shells[shells.length - 1] : null;
}

function showResumeUpload(){
  const chat = document.getElementById('chatArea');
  if(!chat) return null;

  let shell = getResumeUploadShell();
  if(!shell){
    shell = document.createElement('div');
    shell.className = 'resume-upload-shell';
    shell.id = 'resumeUploadShell';
    shell.innerHTML = `
      <div class="file-upload-area" id="resumeUploadArea" onclick="triggerResumeFilePick()" ondragover="handleResumeDragOver(event)" ondragleave="handleResumeDragLeave(event)" ondrop="handleResumeDrop(event)">
        <div class="file-upload-icon">📄</div>
        <div class="file-upload-title">Перетащите PDF или DOCX сюда или нажмите для выбора</div>
        <div class="file-upload-subtitle">Также можно вставить текст резюме ниже</div>
        <input type="file" id="resumeFileInput" accept=".pdf,.docx,application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document" style="display:none" onchange="if(this.files&&this.files[0])handleResumeFile(this.files[0]);this.value='';">
      </div>
      <div class="file-upload-progress" id="resumeUploadProgress">
        <div class="file-upload-progress-bar">
          <div class="file-upload-progress-fill" id="resumeUploadProgressFill"></div>
        </div>
        <div class="file-upload-status" id="resumeUploadStatus">Загружаю файл...</div>
      </div>
      <div class="file-success" id="resumeUploadSuccess"></div>
    `;
  }

  chat.appendChild(shell);
  chat.scrollTop = chat.scrollHeight;
  return shell;
}

function triggerResumeFilePick(){
  const input = document.getElementById('resumeFileInput');
  if(input) input.click();
}

function handleResumeDragOver(event){
  event.preventDefault();
  const area = document.getElementById('resumeUploadArea');
  if(area) area.classList.add('dragging');
}

function handleResumeDragLeave(event){
  event.preventDefault();
  const area = document.getElementById('resumeUploadArea');
  if(area) area.classList.remove('dragging');
}

function handleResumeDrop(event){
  event.preventDefault();
  const area = document.getElementById('resumeUploadArea');
  if(area) area.classList.remove('dragging');
  const file = event.dataTransfer?.files?.[0];
  if(file) handleResumeFile(file);
}

function setResumeUploadProgress(percent, status){
  const progress = document.getElementById('resumeUploadProgress');
  const fill = document.getElementById('resumeUploadProgressFill');
  const text = document.getElementById('resumeUploadStatus');
  if(!progress || !fill || !text) return;

  progress.classList.add('show');
  fill.style.width = `${Math.max(0, Math.min(percent, 100))}%`;
  text.textContent = status;
}

function hideResumeUploadProgress(){
  const progress = document.getElementById('resumeUploadProgress');
  const fill = document.getElementById('resumeUploadProgressFill');
  const text = document.getElementById('resumeUploadStatus');
  if(progress) progress.classList.remove('show');
  if(fill) fill.style.width = '0%';
  if(text) text.textContent = 'Загружаю файл...';
}

function showResumeUploadSuccess(fileName, chars){
  const success = document.getElementById('resumeUploadSuccess');
  if(!success) return;
  success.textContent = `✓ ${fileName} — ${chars} символов извлечено`;
  success.classList.add('show');
}

function hideResumeUploadSuccess(){
  const success = document.getElementById('resumeUploadSuccess');
  if(!success) return;
  success.classList.remove('show');
  success.textContent = '';
}

function prepareResumeInput(useDraft = true){
  showResumeUpload();
  showInput('Вставьте резюме...');

  if(useDraft && CS.chatDrafts && CS.chatDrafts.resume){
    restoreChatDraft('resume');
    return true;
  }

  if(CS.profile.cv && CS.profile.cv !== 'пропустить'){
    setInputValue(CS.profile.cv);
    return true;
  }

  return false;
}

function resetResumeAnalysisState(preserveDraft = true){
  archiveCurrentChatLog('resume');
  clearStoredChatLog('resume');
  delete CS.dialogFollowups.resume;
  delete CS.dialogModules.resume;
  delete CS.moduleInputs.resume;
  if(!preserveDraft){
    delete CS.chatDrafts.resume;
  }
  appState = 'RESUME';
  saveCS();
}

async function handleResumeFile(file){
  if(!file) return;

  const extension = (file.name.split('.').pop() || '').toLowerCase();
  if(!['pdf', 'docx'].includes(extension)){
    aiMsg('Можно загрузить только PDF или DOCX.');
    return;
  }
  if(file.size > 5 * 1024 * 1024){
    aiMsg('Файл слишком большой. Максимум — 5 MB.');
    return;
  }

  showResumeUpload();
  hideResumeUploadSuccess();
  hideResumeUploadProgress();
  setResumeUploadProgress(5, 'Загружаю файл...');
  window.setTimeout(() => setResumeUploadProgress(70, 'Извлекаю текст...'), 60);

  const formData = new FormData();
  formData.append('resume', file);

  try{
    const res = await fetch(`${API_BASE}/extract_file.php`, {
      method: 'POST',
      credentials: 'include',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-Token': getCsrfToken()
      },
      body: formData
    });

    let data = {};
    try{
      data = await res.json();
    }catch(_err){
      data = {};
    }

    if(!res.ok || typeof data.text !== 'string' || !data.text.trim()){
      throw new Error(data.error || 'Не удалось извлечь текст из файла.');
    }

    setResumeUploadProgress(100, 'Текст готов');
    window.setTimeout(() => {
      hideResumeUploadProgress();
      showResumeUploadSuccess(file.name, Number(data.chars) || data.text.length);
    }, 300);

    resetResumeAnalysisState(false);
    showInput('Вставьте резюме...');
    setInputValue(data.text);
    if(CS && CS.chatDrafts){
      CS.chatDrafts.resume = data.text;
      saveCS();
    }
    aiMsg('Резюме загружено. Нажмите отправить или дополните текст.');
  }catch(err){
    hideResumeUploadProgress();
    hideResumeUploadSuccess();
    aiMsg(err.message || 'Не удалось обработать файл. Попробуйте ещё раз.');
  }
}

function restoreKickoffUI(){
  setHeader('Знакомство','С чего начать');
  hideInput();

  if(!String(CS.profile.track || '').trim()){
    appState='KICKOFF_TRACK';
    addBtns([
      {label:'Разработка',action:inputAction('Разработка')},
      {label:'Продукт / Аналитика',action:inputAction('Продукт / Аналитика')},
      {label:'Менеджмент',action:inputAction('Менеджмент')},
      {label:'Маркетинг',action:inputAction('Маркетинг')},
      {label:'Другое',action:inputAction('Другое')},
    ]);
    showInput('Или введите свой вариант...');
    restoreChatDraft('kickoff');
    return;
  }

  if(!String(CS.profile.role || '').trim()){
    appState='KICKOFF_ROLE';
    showInput('Например: Python-разработчик, менеджер продукта...');
    restoreChatDraft('kickoff');
    return;
  }

  if(!String(CS.profile.dir || '').trim()){
    appState='KICKOFF_DIR';
    addBtns([
      {label:'1 — Мягко',action:inputAction('1')},
      {label:'3 — Баланс',action:inputAction('3')},
      {label:'5 — Прямо',action:inputAction('5')},
    ]);
    showInput();
    restoreChatDraft('kickoff');
    return;
  }

  if(!String(CS.profile.tl || '').trim()){
    appState='KICKOFF_TL';
    addBtns([
      {label:'На этой неделе',action:inputAction('На этой неделе')},
      {label:'Через 1-2 недели',action:inputAction('Через 1-2 недели')},
      {label:'Через месяц',action:inputAction('Через месяц')},
      {label:'Пока не знаю',action:inputAction('Пока не знаю')},
    ]);
    showInput();
    restoreChatDraft('kickoff');
    return;
  }

  if(!String(CS.profile.hist || '').trim()){
    appState='KICKOFF_HIST';
    showInput('Ваш опыт...');
    restoreChatDraft('kickoff');
    return;
  }

  if(!String(CS.profile.cv || '').trim()){
    appState='KICKOFF_CV';
    addBtns([{label:'Пропустить',action:inputAction('пропустить')}]);
    showInput('Вставьте текст резюме...');
    restoreChatDraft('kickoff');
    return;
  }

  appState='HOME';
  hideInput();
  addBtns([
    {label:'🔍 Разобрать вакансию',action:startModuleAction('decode')},
    {label:'📄 Оптимизировать резюме',action:startModuleAction('resume')},
    {label:'💪 Начать тренировку',action:startModuleAction('practice')},
    {label:'🏠 На главную',action:'showHome()'},
  ]);
}

function restoreResumeUI(){
  setHeader('Резюме','Улучшение резюме');
  hideInput();

  if(savedChatLogState('resume') === 'RESUME_DONE'){
    appState='RESUME_DONE';
    dialogFollowupCount = getDialogFollowupCount('resume');
    showResumeUpload();
    if(dialogFollowupCount < DIALOG_MAX_FOLLOWUPS){
      showInput(getDialogPlaceholder('resume'));
    }
    const buttons = getCompletionButtons('resume');
    if(buttons.length){
      addBtns(buttons);
    }
    return;
  }

  appState='RESUME';
  resumeDepth='standard';
  const resumeAwaitingText = Boolean(CS.chatDrafts && CS.chatDrafts.resume)
    || savedChatLogContains('resume', 'Вставьте текст резюме:');

  if(CS.profile.tl==='На этой неделе' && !resumeAwaitingText){
    addBtns([
      {label:'Подготовиться к вопросам', action:startModuleAction('prep')},
      {label:'Настроиться перед собесом', action:startModuleAction('hype')},
      {label:'Всё равно улучшить резюме', action:'forceResume()'},
    ]);
    return;
  }

  addBtns([
    {label:'Быстрый — топ-3 проблемы', action:"setResumeDepth('quick')"},
    {label:'Стандартный — полный разбор', action:"setResumeDepth('standard')"},
    {label:'Глубокий — переписать всё', action:"setResumeDepth('deep')"},
  ]);

  if(resumeAwaitingText){
    prepareResumeInput(true);
  }
}

function restorePracticeUI(){
  setHeader('Тренировка',`Уровень ${CS.drillStage}: ${DRILL_NAMES[CS.drillStage]}`);

  practiceRound = 0;
  practiceQuestion = '';
  practiceAnswer = '';
  practiceQuestions = [];
  practiceConsumed = false;

  const maxDrill = currentUser.plan ? PLANS[currentUser.plan].maxDrill : 2;
  if(CS.drillStage > maxDrill){
    aiMsg(`Для доступа к уровню ${CS.drillStage} нужен тариф повыше.`);
    showUpgrade();
    return;
  }

  if(CS.practiceInProgress){
    practiceRound = Number.isFinite(CS.practiceInProgress.round) ? CS.practiceInProgress.round : 0;
    practiceQuestion = String(CS.practiceInProgress.question || '');
    practiceAnswer = String(CS.practiceInProgress.answer || '');
    practiceQuestions = Array.isArray(CS.practiceInProgress.questions) ? [...CS.practiceInProgress.questions] : [];
    practiceConsumed = Boolean(CS.practiceInProgress.consumed);

    const savedState = String(CS.practiceInProgress.state || savedChatLogState('practice') || '');
    if(savedState === 'PRACTICE_ANS'){
      appState = 'PRACTICE_ANS';
      showInput('Ваш ответ... (или используйте микрофон)');
      restoreChatDraft('practice');
      return;
    }

    if(savedState === 'PRACTICE_SELF'){
      appState = 'PRACTICE_SELF';
      showInput('5 оценок через пробел...');
      restoreChatDraft('practice');
      return;
    }

    if(savedState === 'PRACTICE_WARMUP_DONE'){
      appState = 'PRACTICE_WARMUP_DONE';
      hideInput();
      addBtns([
        {label:'Первый оцениваемый раунд',action:'runDrill()'},
        {label:'Начать заново',action:"restartModule('practice')"},
      ]);
      return;
    }

    if(savedState === 'PRACTICE_SCORED'){
      appState = 'PRACTICE_SCORED';
      hideInput();
      addBtns([
        {label:'Следующий раунд',action:'runDrill()'},
        {label:'Начать заново',action:"restartModule('practice')"},
        {label:'🏠 На главную',action:'showHome()'},
      ]);
      return;
    }

    hideInput();
    addBtns([
      {label:'Продолжить',action:'runDrill()'},
      {label:'Начать заново',action:"restartModule('practice')"},
    ]);
    return;
  }

  hideInput();
  addBtns([{label:'Начать',action:'runDrill()'}]);
}

function restoreMockUI(){
  setHeader('Пробное интервью','Пробное интервью');

  if(savedChatLogState('mock') === 'MOCK_DONE' && !CS.mockInProgress){
    appState='MOCK_DONE';
    hideInput();
    addBtns([
      {label:'Ещё раз',action:"restartModule('mock')"},
      {label:'🏠 На главную',action:'showHome()'},
    ]);
    return;
  }

  if(CS.mockInProgress && Array.isArray(CS.mockInProgress.questions) && Array.isArray(CS.mockInProgress.answers)){
    mockQuestions = [...CS.mockInProgress.questions];
    mockAnswers = [...CS.mockInProgress.answers];
    mockIndex = Number.isFinite(CS.mockInProgress.index) ? CS.mockInProgress.index : mockQuestions.length;
    renderMockProgressHistory();

    if(mockQuestions.length > mockAnswers.length){
      appState='MOCK_ANS';
      showInput('Ваш ответ...');
      restoreChatDraft('mock');
      return;
    }

    if(mockIndex >= 4 && mockAnswers.length >= 4){
      appState='MOCK_SELF';
      showInput('5 оценок через пробел...');
      restoreChatDraft('mock');
      return;
    }

    hideInput();
    addBtns([
      {label:'Продолжить', action:'resumeMock()'},
      {label:'Начать заново', action:'clearAndStartMock()'},
    ]);
    return;
  }

  hideInput();
  addBtns([{label:'Начать интервью',action:'askMQ()'}]);
}

function renderMockProgressHistory(){
  const chat = document.getElementById('chatArea');
  if(!chat || chat.querySelector('.msg') || !mockQuestions.length) return;

  mockQuestions.forEach((question, index) => {
    aiMsg(`**Вопрос ${index + 1}/4:**\n\n${question}`);
    const answer = String(mockAnswers[index] || '').trim();
    if(answer){
      userMsg(answer);
    }
  });
}

function restoreModuleUI(id){
  switch(id){
    case 'kickoff':
      restoreKickoffUI();
      break;
    case 'decode':
      setHeader('Разбор вакансии','Анализ вакансии');
      if(savedChatLogState('decode') === 'DECODE_DONE'){
        appState='DECODE_DONE';
        dialogFollowupCount = getDialogFollowupCount('decode');
        if(dialogFollowupCount < DIALOG_MAX_FOLLOWUPS){
          showInput(getDialogPlaceholder('decode'));
        } else {
          hideInput();
        }
        addBtns(getCompletionButtons('decode'));
        break;
      }
      appState='DECODE';
      showInput('Вставьте описание вакансии...');
      restoreChatDraft('decode');
      break;
    case 'resume':
      restoreResumeUI();
      break;
    case 'pitch':
      setHeader('Самопрезентация','Рассказ о себе');
      if(savedChatLogState('pitch') === 'PITCH_DONE'){
        appState='PITCH_DONE';
        dialogFollowupCount = getDialogFollowupCount('pitch');
        if(dialogFollowupCount < DIALOG_MAX_FOLLOWUPS){
          showInput(getDialogPlaceholder('pitch'));
        } else {
          hideInput();
        }
        addBtns(getCompletionButtons('pitch'));
        restoreChatDraft('pitch');
        break;
      }
      appState='PITCH';
      addBtns([{label:'Составить рассказ',action:'runPitch()'}]);
      showInput('Добавьте контекст или нажмите кнопку...');
      restoreChatDraft('pitch');
      break;
    case 'practice':
      restorePracticeUI();
      break;
    case 'mock':
      restoreMockUI();
      break;
    case 'prep':
      setHeader('Подготовка к собеседованию','Вероятные вопросы');
      if(savedChatLogState('prep') === 'PREP_DONE'){
        appState='PREP_DONE';
        dialogFollowupCount = getDialogFollowupCount('prep');
        if(dialogFollowupCount < DIALOG_MAX_FOLLOWUPS){
          showInput(getDialogPlaceholder('prep'));
        } else {
          hideInput();
        }
        addBtns(getCompletionButtons('prep'));
        break;
      }
      appState='PREP';
      showInput('Описание вакансии или компании...');
      restoreChatDraft('prep');
      break;
    case 'concerns':
      setHeader('Возражения рекрутера','Ответы на возражения');
      if(savedChatLogState('concerns') === 'CONCERNS_DONE'){
        appState='CONCERNS_DONE';
        dialogFollowupCount = getDialogFollowupCount('concerns');
        if(dialogFollowupCount < DIALOG_MAX_FOLLOWUPS){
          showInput(getDialogPlaceholder('concerns'));
        } else {
          hideInput();
        }
        addBtns(getCompletionButtons('concerns'));
        restoreChatDraft('concerns');
        break;
      }
      appState='CONCERNS_SELF';
      showInput('Ваши предположения о возражениях рекрутера...');
      restoreChatDraft('concerns');
      break;
    case 'stories':
      setHeader('Мои истории','Истории из опыта');
      appState='STORY_ADD';
      showInput('Расскажите историю из рабочего опыта...');
      restoreChatDraft('stories');
      break;
    case 'hype':
      setHeader('Настрой перед собесом','Настрой перед собесом');
      if(savedChatLogState('hype') === 'HYPE_DONE'){
        appState='HYPE_DONE';
        hideInput();
        break;
      }
      appState='HYPE';
      addBtns([
        {label:'Нервничаю, собес скоро',action:inputAction('Нервничаю, собеседование скоро')},
        {label:'Нужна уверенность',action:inputAction('Нужно поднять уверенность в себе')},
        {label:'Боюсь сложных вопросов',action:inputAction('Боюсь что не отвечу на сложные вопросы')},
      ]);
      showInput('Или опишите своё состояние...');
      restoreChatDraft('hype');
      break;
    case 'salary':
      setHeader('Зарплатные ожидания','Как называть сумму');
      if(savedChatLogState('salary') === 'SALARY_DONE'){
        appState='SALARY_DONE';
        dialogFollowupCount = getDialogFollowupCount('salary');
        if(dialogFollowupCount < DIALOG_MAX_FOLLOWUPS){
          showInput(getDialogPlaceholder('salary'));
        } else {
          hideInput();
        }
        addBtns(getCompletionButtons('salary'));
        break;
      }
      appState='SALARY';
      showInput('Ваши данные о зарплатах...');
      restoreChatDraft('salary');
      break;
    case 'negotiate':
      setHeader('Переговоры по офферу','Переговоры по офферу');
      if(savedChatLogState('negotiate') === 'NEGOTIATE_DONE'){
        appState='NEGOTIATE_DONE';
        dialogFollowupCount = getDialogFollowupCount('negotiate');
        if(dialogFollowupCount < DIALOG_MAX_FOLLOWUPS){
          showInput(getDialogPlaceholder('negotiate'));
        } else {
          hideInput();
        }
        addBtns(getCompletionButtons('negotiate'));
        break;
      }
      appState='NEGOTIATE';
      showInput('Опишите ваш оффер...');
      restoreChatDraft('negotiate');
      break;
  }
}

function saveUser(){
  if(currentUser){
    document.getElementById('sbUses').textContent=currentUser.usesLeft||0;
  }
}

async function doLogout(){
  clearTimeout(stateSyncTimer);
  try{
    await requestJSON(`${API_BASE}/auth.php?action=logout`,{method:'POST'});
  }catch(err){
    console.warn('Logout failed', err);
  }
  currentUser=null; CS=null;
  document.getElementById('app').style.display='none';
  window.location.href='/';
}

// ====== APP INIT ======
function enterApp(){
  document.getElementById('app').style.display='block';
  updateSidebar();
  showHome();
}

function updateSidebar(){
  const p=currentUser.plan;
  document.getElementById('sbName').textContent=currentUser.name||'Пользователь';
  document.getElementById('sbRole').textContent=CS.profile.role||'Профиль не создан';
  const planEl=document.getElementById('sbPlan');
  if(!p){planEl.textContent='Пробный';planEl.className='sb-profile-plan plan-none';}
  else if(p==='start'){planEl.textContent='Старт';planEl.className='sb-profile-plan plan-start';}
  else if(p==='prep'){planEl.textContent='Подготовка';planEl.className='sb-profile-plan plan-prep';}
  else if(p==='offer'){planEl.textContent='Оффер';planEl.className='sb-profile-plan plan-offer';}
  document.getElementById('sbUses').textContent=currentUser.usesLeft||0;

  const nav=document.getElementById('sbNav');
  nav.innerHTML='';
  const sections={base:'Основное',train:'Тренировки',offer:'Оффер'};
  const grouped={};
  MODULES.forEach(m=>{
    if(!grouped[m.section])grouped[m.section]=[];
    grouped[m.section].push(m);
  });
  for(const[sec,label] of Object.entries(sections)){
    if(!grouped[sec])continue;
    const div=document.createElement('div');
    div.className='sb-section';
    div.innerHTML=`<div class="sb-section-title">${label}</div>`;
    grouped[sec].forEach(m=>{
      const locked=!canAccess(m.id);
      const item=document.createElement('div');
      item.className='sb-item'+(locked?' locked':'');
      item.innerHTML=`<span class="icon">${m.icon}</span>${m.name}${locked?'<span class="lock">🔒</span>':''}`;
      if(!locked) item.onclick=()=>{startModule(m.id);document.getElementById('sidebar').classList.remove('open');};
      div.appendChild(item);
    });
    nav.appendChild(div);
  }
  const workSection=document.createElement('div');
  workSection.className='sb-section';
  workSection.innerHTML='<div class="sb-section-title">Работа</div>';
  const workItem=document.createElement('div');
  workItem.className='sb-item';
  workItem.innerHTML='<span class="icon">📋</span>Каналы с вакансиями';
  workItem.onclick=()=>{
    showChannelsPage();
    document.getElementById('sidebar').classList.remove('open');
  };
  workSection.appendChild(workItem);
  nav.appendChild(workSection);
}

function canAccess(modId){
  const mod=MODULES.find(m=>m.id===modId);
  if(!mod)return false;
  if(mod.allPlans)return true;
  const plan=currentUser.plan;
  if(!plan)return false;
  return PLANS[plan].modules.includes(modId);
}

async function decUses(){
  if(!currentUser){
    showAuth(DEFAULT_AUTH_MODE);
    return false;
  }
  try{
    const data = await requestJSON(`${API_BASE}/auth.php?action=consume`,{
      method:'POST',
      body:JSON.stringify({
        reason:appState,
        module:(CS && CS.profile && CS.profile.lastModule) ? CS.profile.lastModule : null
      })
    });
    syncCurrentUser(data.user);
    saveUser();
    return true;
  }catch(err){
    if(err.status === 403 && err.data && err.data.code === 'email_not_verified'){
      if(err.data.user){
        syncCurrentUser(err.data.user);
        saveUser();
      }
      pendingVerificationEmail = (err.data && err.data.user && err.data.user.email) || pendingVerificationEmail;
      showAuth('verify_email');
      return false;
    }
    if(err.status === 403){
      if(err.data && err.data.user){
        syncCurrentUser(err.data.user);
        saveUser();
      }
      showUpgrade();
      return false;
    }
    if(err.status === 401){
      showAuth(DEFAULT_AUTH_MODE);
      return false;
    }
    aiMsg('Не удалось обновить лимит сессий. Попробуйте ещё раз.');
    return false;
  }
}

async function refundLastConsume(module, reason='ai_error'){
  try{
    const data = await requestJSON(`${API_BASE}/auth.php?action=refund`,{
      method:'POST',
      body:JSON.stringify({module, reason})
    });
    syncCurrentUser(data.user);
    saveUser();
    return true;
  }catch(err){
    if(err.data && err.data.user){
      syncCurrentUser(err.data.user);
      saveUser();
    }
    console.error(`[${module}] Refund failed`, err);
    return false;
  }
}

function getBillingModule(module){
  const value = String(module || '').trim();
  if(value.startsWith('resume_')) return 'resume';
  if(value.startsWith('practice_')) return 'practice';
  if(value.startsWith('mock_')) return 'mock';
  return value;
}

async function getPaidAIResponse(module, prompt){
  showTyping();
  const billingModule = getBillingModule(module);
  try{
    const resp = await callAI(module, prompt);
    if(isAIErrorResponse(resp)){
      console.error(`[${module}] AI response error`, resp);
      await refundLastConsume(billingModule, resp);
      aiMsg('Не удалось получить ответ. Сессия не списана.');
      return null;
    }
    return resp;
  }catch(err){
    console.error(`[${module}] Unexpected AI error`, err);
    await refundLastConsume(billingModule, err?.message || 'unexpected_error');
    aiMsg('Не удалось получить ответ. Сессия не списана.');
    return null;
  }finally{
    hideTyping();
  }
}

// ====== HOME SCREEN ======
function showHome(){
  const currentModule = CS.profile.lastModule;
  if (shouldPersistCurrentView(currentModule)) {
    saveChatDraft(currentModule);
    saveChatLog(currentModule);
  }
  appState='HOME';
  chatHistory=[];
  const chat=document.getElementById('chatArea');
  document.getElementById('inputArea').style.display='none';
  document.getElementById('mainTitle').textContent='Главная';
  document.getElementById('mainSub').textContent='';

  let html=`<div class="home-screen">
    <div class="home-greeting">Привет, <span>${esc(currentUser.name||'друг')}</span>!</div>
    <div class="home-sub">${CS.profile.role?'Продолжайте подготовку':'Начните со знакомства'}</div>
    <div class="home-modules">`;

  MODULES.forEach(m=>{
    const locked=!canAccess(m.id);
    html+=`<div class="home-mod${locked?' locked':''}" ${locked?'':`onclick="startModule('${m.id}')"`}>
      <div class="mod-icon">${m.icon}</div>
      <div class="mod-name">${m.name}</div>
      <div class="mod-desc">${m.desc}${locked?' 🔒':''}</div>
    </div>`;
  });

  html+=`</div>`;

  if(CS.drillStage>1||CS.scoreHistory.length>0){
    const pct=Math.round((CS.drillStage-1)/8*100);
    html+=`<div class="home-progress">
      <h3>Прогресс тренировок</h3>
      <div class="progress-bar-wrap"><div class="progress-bar-fill" style="width:${pct}%"></div></div>
      <div class="progress-stats">
        <span>Уровень: <strong>${DRILL_NAMES[CS.drillStage]||CS.drillStage} (${CS.drillStage}/8)</strong></span>
        <span>Оценённых ответов: <strong>${CS.sessionCount}</strong></span>
        <span>Историй: <strong>${CS.storybank.length}</strong></span>
      </div>
    </div>`;
  }

  if(!currentUser.plan){
    html+=`<div class="home-trial-note">
      <p class="home-trial-copy">У вас 3 бесплатные пробные сессии. Для полного доступа выберите тариф.</p>
      <button class="btn-primary home-trial-cta" onclick="showUpgrade()">Выбрать тариф</button>
    </div>`;
  }

  html+=`</div>`;
  chat.innerHTML=html;
}

// ====== MODULE ROUTER ======
function startModule(id){
  const previousModule = CS.profile.lastModule;
  if (previousModule && previousModule !== id && shouldPersistCurrentView(previousModule)) {
    saveChatDraft(previousModule);
    saveChatLog(previousModule);
  }
  if(!canAccess(id)){showUpgrade();return;}
  if(id !== 'kickoff' && !String(CS.profile.role || '').trim()){
    aiMsg('Сначала создайте профиль — это займёт 2 минуты и сделает все ответы точнее.');
    addBtns([
      {label:'Создать профиль', action:"startModule('kickoff')"},
      {label:'Продолжить без профиля', action:`forceStartModule('${id}')`},
    ]);
    return;
  }
  forceStartModule(id);
}

function forceStartModule(id){
  document.getElementById('sidebar').querySelectorAll('.sb-item').forEach(el=>el.classList.remove('active'));
  const items=document.getElementById('sidebar').querySelectorAll('.sb-item');
  items.forEach(el=>{if(el.textContent.includes(MODULES.find(m=>m.id===id)?.name))el.classList.add('active');});

  const chat=document.getElementById('chatArea');
  chat.innerHTML='';
  chatHistory=[];
  dialogFollowupCount = 0;

  const hasRestoredLog = restoreChatLog(id);

  CS.profile.lastModule=id;
  saveCS();

  if(hasRestoredLog){
    restoreModuleUI(id);
    return;
  }

  switch(id){
    case 'kickoff': startKickoff(); break;
    case 'decode': startDecode(); break;
    case 'resume': startResume(); break;
    case 'pitch': startPitch(); break;
    case 'practice': startPractice(); break;
    case 'mock': startMock(); break;
    case 'prep': startPrep(); break;
    case 'concerns': startConcerns(); break;
    case 'stories': startStories(); break;
    case 'hype': startHype(); break;
    case 'salary': startSalary(); break;
    case 'negotiate': startNegotiate(); break;
  }
}

// ====== UI HELPERS ======
function setHeader(title,sub=''){
  document.getElementById('mainTitle').textContent=title;
  document.getElementById('mainSub').textContent=sub;
}

function renderChannelCardMembers(members){
  const value=String(members || '').trim();
  if(!value || value.toLowerCase()==='скоро'){
    return '<div class="channel-card-members"><span class="channel-card-badge">скоро</span></div>';
  }
  return `<div class="channel-card-members">👥 ${esc(value)}</div>`;
}

function renderAllCategories(filterCat='Все'){
  return CHANNELS_DATA
    .filter(group => filterCat === 'Все' || group.category === filterCat)
    .map(group => {
      const channels = Array.isArray(group.channels) ? group.channels : [];
      let gridHtml = '';

      if(!channels.length){
        gridHtml = '<div class="channels-empty">Каналы появятся скоро</div>';
      } else {
        gridHtml = channels.map(channel => `
          <a class="channel-card"
             href="${esc(channel.url || '#')}"
             target="_blank"
             rel="noopener noreferrer">
            <div class="channel-card-name">${esc(channel.name || '')}</div>
            <div class="channel-card-desc">${esc(channel.desc || '')}</div>
            ${renderChannelCardMembers(channel.members)}
          </a>
        `).join('');
      }

      return `
        <div class="channels-category">
          <div class="channels-cat-title">${esc(group.icon || '📋')} ${esc(group.category || '')}</div>
          <div class="channels-grid">${gridHtml}</div>
        </div>
      `;
    }).join('');
}

function filterChannels(category){
  document.querySelectorAll('.channels-filter-btn').forEach(btn=>{
    btn.classList.toggle('active', btn.dataset.category === category);
  });
  const content=document.getElementById('channelsContent');
  if(content){
    content.innerHTML = renderAllCategories(category);
  }
}

function showChannelsPage() {
  const currentModule = CS.profile.lastModule;
  if (shouldPersistCurrentView(currentModule)) {
    saveChatDraft(currentModule);
    saveChatLog(currentModule);
  }

  appState = 'CHANNELS';
  chatHistory = [];

  setHeader('Каналы с вакансиями', 'Отобранные Telegram-каналы');
  hideInput();

  document.getElementById('sidebar').querySelectorAll('.sb-item').forEach(el=>el.classList.remove('active'));
  document.getElementById('sidebar').querySelectorAll('.sb-item').forEach(el=>{
    if(el.textContent.includes('Каналы с вакансиями'))el.classList.add('active');
  });

  const chat = document.getElementById('chatArea');
  const allCategories = ['Все', ...CHANNELS_DATA.map(c => c.category)];

  let filtersHtml = '<div class="channels-filters">';
  allCategories.forEach((cat, i) => {
    const active = i === 0 ? 'active' : '';
    filtersHtml += `<button class="channels-filter-btn ${active}" data-category="${esc(cat)}" onclick='filterChannels(${JSON.stringify(cat)})'>${esc(cat)}</button>`;
  });
  filtersHtml += '</div>';

  let html = `<div class="channels-page">
    <div class="channels-header">
      <h2>📋 Каналы с вакансиями</h2>
      <p>Отобранные Telegram-каналы для поиска работы.
         Новые каналы появляются регулярно.</p>
    </div>
    ${filtersHtml}
    <div id="channelsContent">
      ${renderAllCategories()}
    </div>
  </div>`;

  chat.innerHTML = html;
  chat.scrollTop = 0;
}

function showInput(placeholder='Введите сообщение...'){
  document.getElementById('inputArea').style.display='';
  document.getElementById('userInput').placeholder=placeholder;
  document.getElementById('userInput').value='';
  document.getElementById('userInput').style.height='auto';
  document.getElementById('userInput').focus();
}

function hideInput(){
  document.getElementById('inputArea').style.display='none';
}

function aiMsg(text,extra=''){
  const chat=document.getElementById('chatArea');
  const div=document.createElement('div');
  div.className='msg ai';
  const time=new Date().toLocaleTimeString('ru-RU',{hour:'2-digit',minute:'2-digit'});
  div.innerHTML=`<div class="msg-bubble">${fmt(text)}${extra}</div><div class="msg-time">${time}</div>`;
  chat.appendChild(div);
  chat.scrollTop=chat.scrollHeight;
  queueChatLogSave();
  return div;
}

function userMsg(text){
  const chat=document.getElementById('chatArea');
  const div=document.createElement('div');
  div.className='msg user';
  const time=new Date().toLocaleTimeString('ru-RU',{hour:'2-digit',minute:'2-digit'});
  div.innerHTML=`<div class="msg-bubble">${esc(trimInput(String(text || ''), 5000, 'user.message'))}</div><div class="msg-time">${time}</div>`;
  chat.appendChild(div);
  chat.scrollTop=chat.scrollHeight;
  queueChatLogSave();
}

function addBtns(btns){
  const chat=document.getElementById('chatArea');
  const div=document.createElement('div');
  div.className='msg ai';
  let html='<div class="msg-buttons">';
  btns.forEach(b=>{
    html+=`<button class="msg-btn" onclick="if(!isProcessing){${b.action}}">${b.label}</button>`;
  });
  html+='</div>';
  div.innerHTML=html;
  chat.appendChild(div);
  chat.scrollTop=chat.scrollHeight;
  queueChatLogSave();
}

function showTyping(){
  const chat=document.getElementById('chatArea');
  const div=document.createElement('div');
  div.className='typing-indicator';
  div.id='typingIndicator';
  div.innerHTML='<div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div>';
  chat.appendChild(div);
  chat.scrollTop=chat.scrollHeight;
}

function hideTyping(){
  const el=document.getElementById('typingIndicator');
  if(el)el.remove();
}

function fmt(text){
  if(!text)return'';
  let h=esc(normalizeDisplayMarkdown(text));
  h=h.replace(/\*\*(.+?)\*\*/g,'<strong>$1</strong>');
  h=h.replace(/^•\s+/gm,'&#8226; ');
  h=h.replace(/\n\n/g,'<br><br>');
  h=h.replace(/\n/g,'<br>');
  h=h.replace(/`(.+?)`/g,'<code class="inline-msg-code">$1</code>');
  h=fmtSc(h);
  return h;
}

function normalizeDisplayMarkdown(text){
  let value=normalizeModuleMentions(String(text || '').replace(/\r\n?/g,'\n'));
  value=value.replace(/^\|(.+)\|$/gm, (_, row) => {
    const cells = row
      .split('|')
      .map(part => part.trim())
      .filter(Boolean);

    if(cells.length === 0){
      return '';
    }
    if(cells.every(cell => /^:?-{2,}:?$/.test(cell))){
      return '';
    }
    if(cells.length === 1){
      return cells[0];
    }

    const [head, ...rest] = cells;
    return `${head}: ${rest.join('; ')}`;
  });
  value=value.replace(/^#{1,6}\s+/gm,'');
  value=value.replace(/^(?:---+|\*\*\*+)\s*$/gm,'');
  value=value.replace(/^\|[-|\s:]+\|$/gm,'');
  value=value.replace(/^>\s?/gm,'');
  value=value.replace(/^\s*-\s+/gm,'• ');
  value=value.replace(/(^|[^\*])\*([^*\n]+)\*(?=[^\*]|$)/g,'$1$2');
  value=value.replace(/(^|[^_])_([^_\n]+)_(?=[^_]|$)/g,'$1$2');
  value=value.replace(/\n{3,}/g,'\n\n');
  return value.trim();
}

function esc(s){
  const d=document.createElement('div');
  d.textContent=s;
  return d.innerHTML;
}

function rxEscape(s){
  return s.replace(/[.*+?^${}()|[\]\\]/g,'\\$&');
}

function normalizeModuleMentions(text){
  let value = String(text || '');
  const aliases = {
    resume_review:'Резюме',
    resume_standard:'Резюме',
    resume_quick:'Резюме',
    resume_deep:'Резюме',
    kickoff_analysis:'Знакомство',
    mock_debrief:'Пробное интервью',
    mock_q:'Пробное интервью',
    practice_q:'Тренировка',
    practice_score:'Тренировка',
    prep:'Подготовка к собеседованию',
    concerns:'Возражения рекрутера',
    story:'Мои истории',
    pitch:'Самопрезентация',
    decode:'Разбор вакансии',
    salary:'Зарплатные ожидания',
    negotiate:'Переговоры по офферу',
  };

  Object.entries(aliases).forEach(([key, label]) => {
    value = value.replace(new RegExp(`\\b${rxEscape(key)}\\b`, 'gi'), label);
  });

  return value.replace(/\s+→\s+/g, ': ');
}

function normalizeComparableText(text){
  return normalizeDisplayMarkdown(text)
    .toLowerCase()
    .replace(/[^\p{L}\p{N}]+/gu, ' ')
    .trim();
}

function sanitizeResumeOutput(text, sourceText){
  let value = String(text || '');
  value = value.replace(/\b[Xx]\s*\/\s*[Yy]\b/g, '[нужна цифра] / [нужен результат]');

  const allowedSource = String(sourceText || '').toLowerCase();
  const guardedTerms = [
    'FastAPI','Django','Flask','Laravel','Symfony','Spring','React','Vue','Angular',
    'Next.js','NextJS','NestJS','Express','Ruby on Rails','Go','Golang','Java',
    'Kotlin','Swift','Docker','Kubernetes','Kafka','RabbitMQ','Redis','PostgreSQL',
    'MySQL','MongoDB','GraphQL','gRPC','AWS','GCP','Azure','Jenkins','GitLab CI',
  ];

  guardedTerms.forEach(term => {
    if(allowedSource.includes(term.toLowerCase())){
      return;
    }
    value = value.replace(new RegExp(`\\b${rxEscape(term)}\\b`, 'gi'), '[добавьте технологию]');
  });

  const guardedClaims = [
    {
      allowIf:'нагруз',
      pattern:/реальн(?:ой|ыми|ая|ое)?\s+нагрузк(?:ой|а|е|и|у)?/gi,
      replacement:'[уточните масштаб задач]'
    },
    {
      allowIf:'нагруз',
      pattern:/высок(?:ой|ими|ая|ое)?\s+нагрузк(?:ой|а|е|и|у)?/gi,
      replacement:'[уточните масштаб задач]'
    },
    {
      allowIf:'ai-инфраструкт',
      pattern:/ai-инфраструктур(?:а|е|ы|у|ой)?/gi,
      replacement:'[уточните профильный интерес]'
    },
    {
      allowIf:'интерес к ai',
      pattern:/интерес[^.\n]{0,40}\s+к\s+ai(?:-инфраструктур(?:е|е|ой|у|а|ы)?)?/gi,
      replacement:'[уточните мотивацию]'
    },
    {
      allowIf:'внутренн',
      pattern:/внутренн(?:ими|их|ие|яя|ее)?\s+платформ(?:ами|ы|е|у|а)?/gi,
      replacement:'[уточните тип продукта]'
    },
  ];

  guardedClaims.forEach(rule => {
    if(allowedSource.includes(rule.allowIf)){
      return;
    }
    value = value.replace(rule.pattern, rule.replacement);
  });

  return value;
}

function normalizeSensitiveToken(token){
  return String(token || '')
    .toLowerCase()
    .replace(/[ \u00a0]/g, '')
    .replace(/[–—]/g, '-')
    .replace(/руб(?:лей|ля|\.|)?/g, '₽')
    .replace(/тыс\.?/g, 'к')
    .replace(/т\.р\./g, 'к');
}

function collectSensitiveTokens(text){
  const value = String(text || '');
  const tokens = new Set();
  const patterns = [
    /(?:\d{2,3}(?:[ \u00A0]?\d{3})?|\d+)\s*(?:[-–—]\s*(?:\d{2,3}(?:[ \u00A0]?\d{3})?|\d+)\s*)?(?:к|k|тыс\.?|т\.р\.?|₽|руб(?:лей|ля|\.|)?)/gi,
    /(?:\d+\s*(?:[-–—]\s*\d+\s*)?(?:месяц(?:а|ев)?|мес\.?))/gi,
    /\d+\s*%/g,
  ];

  patterns.forEach(pattern => {
    const matches = value.match(pattern) || [];
    matches.forEach(match => tokens.add(normalizeSensitiveToken(match)));
  });

  return tokens;
}

function sanitizeSensitiveNumberOutput(text, sourceText){
  let value = String(text || '');
  const allowedTokens = collectSensitiveTokens(sourceText);

  value = value.replace(
    /(?:\d{2,3}(?:[ \u00A0]?\d{3})?|\d+)\s*(?:[-–—]\s*(?:\d{2,3}(?:[ \u00A0]?\d{3})?|\d+)\s*)?(?:к|k|тыс\.?|т\.р\.?|₽|руб(?:лей|ля|\.|)?)/gi,
    match => allowedTokens.has(normalizeSensitiveToken(match)) ? match : '[ваша сумма]'
  );
  value = value.replace(
    /(?:\d+\s*(?:[-–—]\s*\d+\s*)?(?:месяц(?:а|ев)?|мес\.?))/gi,
    match => allowedTokens.has(normalizeSensitiveToken(match)) ? match : '[срок нужно уточнить]'
  );
  value = value.replace(
    /\d+\s*%/g,
    match => allowedTokens.has(normalizeSensitiveToken(match)) ? match : '[процент нужно уточнить]'
  );

  return value;
}

function sanitizeMockDebriefOutput(text){
  return String(text || '')
    .replace(/^\s*Самооценка:[^\n]*$/gmi, '')
    .replace(/\n{3,}/g, '\n\n')
    .trim();
}

function sanitizeModuleResponse(module, text, sourceText=''){
  const normalizedModule = String(module || '').startsWith('resume_')
    ? 'resume'
    : String(module || '');

  let value = String(text || '');

  if(normalizedModule === 'resume'){
    value = sanitizeResumeOutput(value, sourceText);
  }
  if(normalizedModule === 'salary' || normalizedModule === 'negotiate'){
    value = sanitizeSensitiveNumberOutput(value, sourceText);
  }
  if(normalizedModule === 'mock_debrief'){
    value = sanitizeMockDebriefOutput(value);
  }

  return normalizeDisplayMarkdown(value);
}

function findDimensionScore(text, labels){
  for(const label of labels){
    const m=text.match(new RegExp(rxEscape(label)+'[:\\s]*(\\d)[/\\s]*5','i'));
    if(m)return parseInt(m[1]);
  }
  return null;
}

function storySkillLabel(skill){
  const map={
    leadership:'лидерство',
    'problem-solving':'решение проблем'
  };
  return map[skill]||skill||'навык';
}

function fmtSc(html){
  let hasScore=false;
  let scHtml='<div class="scorecard"><div class="sc-title">Оценка по пунктам</div>';
  SCORE_DIMENSIONS.forEach(dim=>{
    const v=findDimensionScore(html, dim.labels);
    if(v!==null){
      hasScore=true;
      const pct=v*20;
      const cls=v>=4?'high':v>=3?'mid':'low';
      scHtml+=`<div class="sc-row"><span class="sc-label">${dim.display}</span><div class="sc-bar"><div class="sc-bar-fill ${cls}" style="width:${pct}%"></div></div><span class="sc-score">${v}/5</span></div>`;
    }
  });
  scHtml+='</div>';

  const hire=html.match(/(?:Оценка интервью|Hire Signal)[:\s]*(Однозначно берём|Берём|Сомнения|Не берём|Strong Hire|Hire|Mixed|No Hire)/i);
  if(hire){
    const raw=hire[1];
    const val=HIRE_SIGNAL_LABELS[raw]||raw;
    const cls=val==='Однозначно берём'?'strong-hire':val==='Берём'?'hire':val==='Сомнения'?'mixed':'no-hire';
    scHtml=scHtml.replace('</div><!--last-->','');
    scHtml+=`<div class="hire-signal ${cls}">${val}</div></div>`;
  }

  return hasScore?html+scHtml:html;
}

function autoGrow(el){
  el.style.height='auto';
  el.style.height=Math.min(el.scrollHeight,120)+'px';
}

function trimInput(text, maxChars, moduleName) {
  if (!text) return '';
  if (text.length <= maxChars) return text;
  console.warn(`[${moduleName}] Input trimmed: ${text.length} → ${maxChars} chars`);
  return text.substring(0, maxChars);
}

function trimInputNicely(text, maxChars, moduleName){
  const trimmed = trimInput(text, maxChars, moduleName);
  if(trimmed.length < String(text || '').length){
    const cutAt = Math.max(trimmed.lastIndexOf(' '), trimmed.lastIndexOf('\n'));
    if(cutAt > Math.floor(maxChars * 0.6)){
      return trimmed.substring(0, cutAt).trim();
    }
  }
  return trimmed;
}

function pushAssistantToHistory(text, moduleName = 'assistant'){
  if(!text || isAIErrorResponse(text)) return;
  const cleaned = normalizeDisplayMarkdown(text);
  if(!cleaned) return;
  chatHistory.push({role:'assistant', content:trimInput(cleaned, 3000, moduleName)});
}

function isAIErrorResponse(text){
  const value = String(text || '').trim();
  return value === 'Ошибка API'
    || value === 'Сессия истекла. Войдите снова.'
    || value === 'Лимит сессий исчерпан.'
    || value === 'Email не подтверждён.'
    || value.includes('Ошибка соединения с API')
    || value.includes('Слишком много запросов')
    || value.includes('Этот раздел недоступен')
    || value.includes('Неизвестный AI-модуль');
}

function validateSelfScore(text){
  const nums = text.trim().split(/\s+/).map(Number);
  if(nums.length !== 5 || nums.some(n => Number.isNaN(n) || n < 1 || n > 5)){
    aiMsg('Введите ровно 5 цифр от 1 до 5 через пробел. Например: 3 4 3 2 3');
    showInput('5 оценок через пробел...');
    return false;
  }
  return true;
}

// ====== SEND MESSAGE ======
function sendMsg(){
  if(isProcessing)return;
  const input=document.getElementById('userInput');
  const text=input.value.trim();
  if(!text)return;
  input.value='';
  input.style.height='auto';
  if(text.length>5000){
    aiMsg('Текст очень длинный — я возьму первую часть для анализа. Для лучшего результата вставляйте самое важное.');
  }
  const currentModule = CS?.profile?.lastModule;
  if(currentModule && CS?.chatDrafts && CS.chatDrafts[currentModule]){
    delete CS.chatDrafts[currentModule];
    saveCS();
  }
  handleInput(text);
}

function handleInput(text){
  if(appState==='CHANNELS'){
    showChannelsPage();
    return;
  }

  userMsg(text);

  if(Object.prototype.hasOwnProperty.call(DIALOG_STATES, appState)){
    handleDialogFollowup(text);
    return;
  }

  const trimmedForHistory = text.substring(0, 2000);
  chatHistory.push({role:'user',content:trimmedForHistory});

  switch(appState){
    case 'KICKOFF_TRACK': kStep('track',text); break;
    case 'KICKOFF_ROLE': kStep('role',text); break;
    case 'KICKOFF_DIR': kStep('dir',text); break;
    case 'KICKOFF_TL': kStep('tl',text); break;
    case 'KICKOFF_HIST': kStep('hist',text); break;
    case 'KICKOFF_CV': kStep('cv',text); break;
    case 'DECODE': runDecode(text); break;
    case 'RESUME': runResume(text); break;
    case 'PITCH': runPitch(text); break;
    case 'PRACTICE_ANS': gotAnswer(text); break;
    case 'PRACTICE_SELF':
      if(!validateSelfScore(text)){
        chatHistory.pop();
        return;
      }
      runPracticeScore(text);
      break;
    case 'MOCK_ANS': gotMockAns(text); break;
    case 'MOCK_SELF':
      if(!validateSelfScore(text)){
        chatHistory.pop();
        return;
      }
      runMockDebrief(text);
      break;
    case 'PREP': runPrep(text); break;
    case 'CONCERNS_SELF': runConcerns(text); break;
    case 'STORY_ADD': runAddStory(text); break;
    case 'HYPE': runHype(text); break;
    case 'SALARY': runSalary(text); break;
    case 'NEGOTIATE': runNegotiate(text); break;
    default: aiMsg('Выберите модуль из меню слева.'); break;
  }
}

async function handleDialogFollowup(text){
  const aiModule = DIALOG_STATES[appState];
  const moduleKey = getDialogStorageKey(aiModule);
  const followupModule = getDialogModule(moduleKey, aiModule);
  const used = getDialogFollowupCount(moduleKey);
  if(used >= DIALOG_MAX_FOLLOWUPS){
    aiMsg(
      `Вы использовали все ${DIALOG_MAX_FOLLOWUPS} уточнения ` +
      `для этого раздела. Чтобы продолжить диалог — ` +
      `начните раздел заново или перейдите к следующему шагу.`
    );
    hideInput();
    return;
  }

  const nextUsed = used + 1;
  const remaining = DIALOG_MAX_FOLLOWUPS - nextUsed;
  chatHistory.push({role:'user', content:trimInput(text, 1000, 'dialog')});
  const followupPrompt = nextUsed >= DIALOG_MAX_FOLLOWUPS
    ? `${text}\n\nЭто последнее доступное уточнение в этом разделе. Ответь без встречных вопросов и без приглашения продолжить диалог.`
    : text;

  showTyping();
  const rawResp = await callAI(followupModule, followupPrompt);
  hideTyping();

  if(isAIErrorResponse(rawResp)){
    aiMsg(rawResp);
    showInput(getDialogPlaceholder(moduleKey));
    return;
  }

  const updatedSource = `${getModuleInput(moduleKey)}\n${text}`.trim();
  rememberModuleInput(moduleKey, updatedSource);
  const resp = sanitizeModuleResponse(followupModule, rawResp, updatedSource);
  setDialogFollowupCount(moduleKey, nextUsed);
  pushAssistantToHistory(resp, `${moduleKey}.assistant`);
  aiMsg(resp);

  if(nextUsed >= DIALOG_MAX_FOLLOWUPS){
    aiMsg(
      `Это было последнее уточнение для данного раздела. ` +
      `Переходите к следующему шагу или начните раздел заново.`
    );
    hideInput();
  } else {
    showInput(getDialogPlaceholder(moduleKey));
  }
}

// ====== KICKOFF ======
function startKickoff(){
  setHeader('Знакомство','С чего начать');
  appState='KICKOFF_TRACK';
  aiMsg('Давайте создадим ваш профиль! Я буду задавать вопросы по одному.\n\nВ каком треке вы ищете работу?');
  addBtns([
    {label:'Разработка',action:"handleInput('Разработка')"},
    {label:'Продукт / Аналитика',action:"handleInput('Продукт / Аналитика')"},
    {label:'Менеджмент',action:"handleInput('Менеджмент')"},
    {label:'Маркетинг',action:"handleInput('Маркетинг')"},
    {label:'Другое',action:"handleInput('Другое')"},
  ]);
  showInput('Или введите свой вариант...');
}

function kStep(field,text){
  CS.profile[field]=text;

  switch(field){
    case 'track':
      CS.profile.track=text;
      appState='KICKOFF_ROLE';
      aiMsg('Отлично! На какую роль/должность вы претендуете?');
      showInput('Например: Python-разработчик, менеджер продукта...');
      break;
    case 'role':
      appState='KICKOFF_DIR';
      aiMsg('Насколько прямую обратную связь вы предпочитаете?\n\n1 — очень мягко\n3 — сбалансированно\n5 — максимально прямо');
      addBtns([
        {label:'1 — Мягко',action:"handleInput('1')"},
        {label:'3 — Баланс',action:"handleInput('3')"},
        {label:'5 — Прямо',action:"handleInput('5')"},
      ]);
      showInput();
      break;
    case 'dir':
      appState='KICKOFF_TL';
      aiMsg('Когда ближайшее собеседование?');
      addBtns([
        {label:'На этой неделе',action:"handleInput('На этой неделе')"},
        {label:'Через 1-2 недели',action:"handleInput('Через 1-2 недели')"},
        {label:'Через месяц',action:"handleInput('Через месяц')"},
        {label:'Пока не знаю',action:"handleInput('Пока не знаю')"},
      ]);
      showInput();
      break;
    case 'tl':
      appState='KICKOFF_HIST';
      aiMsg('Расскажите кратко о вашем опыте: сколько лет в профессии, ключевые места работы, основные достижения.');
      showInput('Ваш опыт...');
      break;
    case 'hist':
      appState='KICKOFF_CV';
      aiMsg('Отлично! Теперь вставьте текст вашего резюме (или его основную часть). Если нет под рукой — можно пропустить.');
      addBtns([{label:'Пропустить',action:"handleInput('пропустить')"}]);
      showInput('Вставьте текст резюме...');
      break;
    case 'cv':
      finishKickoff();
      break;
  }
  saveCS();
  updateSidebar();
}

async function finishKickoff(){
  appState='HOME';
  hideInput();
  chatHistory=[];
  showTyping();
  const p=CS.profile;
  const histSummary=trimInputNicely(p.hist || '', 700, 'kickoff_analysis.hist');
  const resumeLoaded=Boolean(p.cv && p.cv !== 'пропустить');
  const recommendation=await callAI('kickoff_analysis',`Кратко проанализируй профиль кандидата и дай персональную рекомендацию, с чего начать подготовку.\n\nРоль: ${p.role || 'не указана'}.\nТрек: ${p.track || 'не указан'}.\nТаймлайн: ${p.tl || 'не указан'}.\nУровень прямоты фидбека: ${p.dir || '3'}/5.\nОпыт: ${histSummary || 'не указан'}.\nРезюме: ${resumeLoaded ? 'загружено' : 'не загружено'}.\n\nНе используй markdown, внутренние идентификаторы модулей и служебные названия. Называй разделы только по продуктовым именам. Формат ответа:\n1. Короткий вывод о профиле.\n2. Главный риск в подготовке.\n3. С какого модуля начать прямо сейчас и почему.\n4. Следующий шаг на сегодня.`);
  hideTyping();
  const kickoffGuidance=isAIErrorResponse(recommendation)
    ? 'Персональную рекомендацию сейчас не удалось получить. Начните с **Разбора вакансии** или **Тренировки**.'
    : `**Персональная рекомендация:**\n${sanitizeModuleResponse('kickoff_analysis', recommendation, `${p.role}\n${p.track}\n${histSummary}`)}`;
  aiMsg(`**Профиль создан!**\n\n**Трек:** ${p.track}\n**Роль:** ${p.role}\n**Прямота фидбека:** ${p.dir}/5\n**Таймлайн:** ${p.tl}\n**Опыт:** ${histSummary || 'Не указан'}\n**Резюме:** ${resumeLoaded?'Загружено':'Не загружено'}\n\n${kickoffGuidance}`);
  addBtns([
    {label:'🔍 Разобрать вакансию',action:"startModule('decode')"},
    {label:'📄 Оптимизировать резюме',action:"startModule('resume')"},
    {label:'💪 Начать тренировку',action:"startModule('practice')"},
    {label:'🏠 На главную',action:"showHome()"},
  ]);
  saveCS();
}

// ====== DECODE JD ======
function startDecode(){
  setHeader('Разбор вакансии','Анализ вакансии');
  appState='DECODE';
  aiMsg('Вставьте текст вакансии — я разберу её по 6 важным пунктам и отмечу, где уверенность высокая, средняя или низкая.');
  showInput('Вставьте описание вакансии...');
}

async function runDecode(text){
  if(!(await decUses()))return;
  appState='DECODE_DONE';
  hideInput();
  const jdText = trimInput(text, 3000, 'decode');
  rememberModuleInput('decode', jdText);
  setDialogFollowupCount('decode', 0);
  setDialogModule('decode', 'decode');
  const rawResp=await getPaidAIResponse('decode',`Проанализируй эту вакансию по 6 линзам. Вакансия:\n${jdText}\n\nНе используй markdown, таблицы и служебные символы. Не заканчивай ответ вопросом. Заверши практическим следующим шагом.`);
  const resp = rawResp ? sanitizeModuleResponse('decode', rawResp, jdText) : null;
  if(!resp){
    appState='DECODE';
    showInput('Вставьте описание вакансии...');
    return;
  }
  CS.lastJD=text;
  saveCS();
  aiMsg(resp);
  pushAssistantToHistory(resp, 'decode.assistant');
  addBtns([
    {label:'📄 Оптимизировать резюме под эту вакансию',action:"startModule('resume')"},
    {label:'🏠 На главную',action:"showHome()"},
  ]);
  showInput(`Уточняющий вопрос (осталось ${DIALOG_MAX_FOLLOWUPS} из ${DIALOG_MAX_FOLLOWUPS})...`);
}

// ====== RESUME ======
function startResume(){
  if(CS.profile.tl==='На этой неделе'){
    setHeader('Резюме','Улучшение резюме');
    hideInput();
    aiMsg('У вас собеседование на этой неделе — сейчас важнее подготовиться к вопросам, а не переделывать резюме.\n\nЧто сделать прямо сейчас?');
    addBtns([
      {label:'Подготовиться к вопросам', action:"startModule('prep')"},
      {label:'Настроиться перед собесом', action:"startModule('hype')"},
      {label:'Всё равно улучшить резюме', action:'forceResume()'},
    ]);
    return;
  }
  forceResume();
}

function forceResume(){
  setHeader('Резюме','Улучшение резюме');
  appState='RESUME';
  resumeDepth='standard';
  hideInput();
  aiMsg('Вставьте текст вашего резюме — я помогу сделать его понятнее, сильнее и убедительнее.');
  aiMsg('Выберите уровень анализа:');
  addBtns([
    {label:'Быстрый — топ-3 проблемы', action:"setResumeDepth('quick')"},
    {label:'Стандартный — полный разбор', action:"setResumeDepth('standard')"},
    {label:'Глубокий — переписать всё', action:"setResumeDepth('deep')"},
  ]);
}

function setResumeDepth(depth){
  resumeDepth=depth;
  resetResumeAnalysisState(true);
  aiMsg('Вставьте текст резюме:');
  prepareResumeInput(true);
}

async function runResume(text){
  if(!(await decUses()))return;
  appState='RESUME_DONE';
  hideInput();
  const resumeText = trimInput(text, 3000, 'resume.text');
  const cvText = (CS.profile.cv && CS.profile.cv !== 'пропустить')
    ? trimInput(CS.profile.cv, 3000, 'resume.cv') : '';
  const jdText = trimInput(CS.lastJD || '', 2000, 'resume.jd');
  const storiesText = CS.storybank.length > 0
    ? CS.storybank.slice(0, 3).map((s,i) =>
        `${i+1}. ${trimInput(s.raw || '', 150, 'resume.story')}`).join('\n')
    : '';
  const depthLabel = resumeDepth==='quick'
    ? 'быстрый (только топ-3 проблемы)'
    : resumeDepth==='deep'
      ? 'глубокий (переписать все буллеты)'
      : 'стандартный (полный разбор)';
  const jdContext = jdText ? `Вакансия для адаптации резюме:\n${jdText}\n\n` : '';
  const storyContext = storiesText
    ? 'Реальные достижения кандидата из его историй — используй их для улучшения буллетов:\n'
      + storiesText
      + '\n\n'
    : '';
  const profileContext = cvText && cvText !== resumeText ? `Сохранённая версия резюме из профиля:\n${cvText}\n\n` : '';
  const resumeFacts = [CS.profile.role || '', cvText, resumeText, storiesText].filter(Boolean).join('\n');
  const resumeModule = 'resume_' + resumeDepth;
  rememberModuleInput('resume', resumeFacts);
  setDialogFollowupCount('resume', 0);
  setDialogModule('resume', resumeModule);
  const rawResp=await getPaidAIResponse(resumeModule,`Разбери и улучши это резюме.\n\nУровень анализа: ${depthLabel}\nРоль: ${CS.profile.role}.\n${jdContext}${profileContext}${storyContext}Используй только факты из текста резюме, сохранённого CV и историй кандидата. Не подтягивай стек, инструменты и технологии из kickoff-профиля, если их нет в этих источниках. Обязательно выдели отдельным блоком: тревожные места резюме (частая смена работ, пробелы, понижение должности) и дай конкретные формулировки, как это объяснить рекрутеру. Запрещено добавлять новый стек, новые технологии, метрики, компании, сроки, нагрузки и интересы, которых нет в данных кандидата. Если факта не хватает, используй [нужно уточнение], [нужна цифра], [добавьте технологию]. Не используй markdown-таблицы, X/Y-шаблоны и служебную разметку.\n\nРезюме:\n${resumeText || cvText}`);
  const resp = rawResp ? sanitizeModuleResponse('resume', rawResp, resumeFacts) : null;
  if(!resp){
    appState='RESUME';
    showResumeUpload();
    showInput('Вставьте резюме...');
    return;
  }
  aiMsg(resp);
  pushAssistantToHistory(resp, 'resume.assistant');
  addBtns([
    {label:'🎙 Подготовить рассказ о себе',action:"startModule('pitch')"},
    {label:'🏠 На главную',action:"showHome()"},
  ]);
  showResumeUpload();
  showInput(`Уточняющий вопрос (осталось ${DIALOG_MAX_FOLLOWUPS} из ${DIALOG_MAX_FOLLOWUPS})...`);
}

// ====== PITCH ======
function startPitch(){
  setHeader('Самопрезентация','Рассказ о себе');
  appState='PITCH';
  aiMsg(`Подготовлю ваш рассказ о себе в трёх форматах: 30, 60 и 90 секунд.\n\nРоль: **${CS.profile.role||'не указана'}**\n\nНажмите "Составить рассказ" или опишите вашу текущую ситуацию подробнее.`);
  addBtns([{label:'Составить рассказ',action:'runPitch()'}]);
  showInput('Добавьте контекст или нажмите кнопку...');
}

async function runPitch(text){
  let pitchRequest = typeof text === 'string' ? text.trim() : '';
  const input = document.getElementById('userInput');
  if(!pitchRequest && input){
    const rawInput = input.value.trim();
    if(rawInput){
      pitchRequest = trimInput(rawInput, 2000, 'pitch.input');
      userMsg(pitchRequest);
      chatHistory.push({role:'user', content:pitchRequest});
      input.value = '';
      input.style.height = 'auto';
      if(CS?.chatDrafts?.pitch){
        delete CS.chatDrafts.pitch;
        saveCS();
      }
    }
  }
  if(!pitchRequest){
    pitchRequest = `составь рассказ о себе для роли ${CS.profile.role || 'не указана'}`;
  }
  if(!(await decUses()))return;
  appState='PITCH_DONE';
  hideInput();
  const histShort = trimInput(CS.profile.hist || '', 400, 'pitch.hist');
  rememberModuleInput('pitch', `${CS.profile.role || ''}\n${histShort}\n${pitchRequest}`);
  setDialogFollowupCount('pitch', 0);
  setDialogModule('pitch', 'pitch');
  const rawResp=await getPaidAIResponse('pitch',`Создай рассказ о себе по схеме "что делаю сейчас -> что делал раньше -> куда иду дальше" для роли ${CS.profile.role}. Контекст: ${histShort}. Запрос: ${pitchRequest}. Пиши обычным текстом без markdown-таблиц, без символов #, >, ---, без служебной разметки. Если факта не хватает, ставь пометку в квадратных скобках, а не выдумывай данные. Не заканчивай ответ вопросом.`);
  const resp = rawResp ? sanitizeModuleResponse('pitch', rawResp, getModuleInput('pitch')) : null;
  if(!resp){
    appState='PITCH';
    showInput('Добавьте контекст или нажмите кнопку...');
    return;
  }
  aiMsg(resp);
  pushAssistantToHistory(resp, 'pitch.assistant');
  addBtns([
    {label:'💪 Начать тренировку',action:"startModule('practice')"},
    {label:'🏠 На главную',action:"showHome()"},
  ]);
  showInput(`Уточняющий вопрос (осталось ${DIALOG_MAX_FOLLOWUPS} из ${DIALOG_MAX_FOLLOWUPS})...`);
}

// ====== PRACTICE ======
function persistPracticeProgress(state = appState){
  if(!CS) return;
  if(!practiceRound && !practiceQuestions.length && !practiceQuestion && !practiceAnswer){
    delete CS.practiceInProgress;
    saveCS();
    return;
  }

  CS.practiceInProgress = {
    round:practiceRound,
    question:String(practiceQuestion || ''),
    answer:String(practiceAnswer || ''),
    questions:[...practiceQuestions],
    consumed:Boolean(practiceConsumed),
    state:String(state || appState || '')
  };
  saveCS();
  saveChatLog('practice');
}

function startPractice(){
  setHeader('Тренировка',`Уровень ${CS.drillStage}: ${DRILL_NAMES[CS.drillStage]}`);
  practiceRound=0;
  practiceQuestion='';
  practiceAnswer='';
  practiceQuestions=[];
  practiceConsumed=false;
  delete CS.practiceInProgress;
  saveCS();
  const maxDrill=currentUser.plan?PLANS[currentUser.plan].maxDrill:2;
  if(CS.drillStage>maxDrill){
    aiMsg(`Для доступа к уровню ${CS.drillStage} нужен тариф повыше.`);
    showUpgrade();
    return;
  }
  aiMsg(`**Тренировка — Уровень ${CS.drillStage}: ${DRILL_NAMES[CS.drillStage]}**\n\nПервый вопрос будет разминкой без оценки. Дальше пойдут оцениваемые раунды без дополнительного списания в рамках этого захода.`);
  addBtns([{label:'Начать',action:'runDrill()'}]);
  hideInput();
}

async function runDrill(){
  const nextRound = practiceRound + 1;
  const histText = trimInputNicely(CS.profile.hist || '', 400, 'practice.hist');
  const jdText = trimInputNicely(CS.lastJD || '', 700, 'practice.jd');
  const storiesText = CS.storybank.length > 0
    ? CS.storybank.slice(0, 2).map((story, index) => `${index + 1}. ${trimInputNicely(story.raw || '', 160, 'practice.story')}`).join('\n')
    : '';
  const previousQuestions = practiceQuestions.length
    ? `Не повторяй вопросы из списка:\n${practiceQuestions.map((question, index) => `${index + 1}. ${question}`).join('\n')}\n\n`
    : '';
  const contextBlock = [
    `Роль: ${CS.profile.role || 'не указана'}.`,
    histText ? `Опыт кандидата: ${histText}` : '',
    jdText ? `Контекст вакансии: ${jdText}` : '',
    storiesText ? `Реальные истории кандидата:\n${storiesText}` : '',
  ].filter(Boolean).join('\n');
  const prompt = `Задай один новый вопрос для тренировки уровня ${DRILL_NAMES[CS.drillStage]} (уровень ${CS.drillStage}). ${nextRound === 1 ? 'Это разминка без оценки.' : `Это оцениваемый раунд ${nextRound - 1}.`} ${previousQuestions}${contextBlock}\n\nВопрос должен быть конкретным, без markdown и без повторов.`;

  chatHistory=[];
  practiceAnswer='';
  appState='PRACTICE_ANS';

  let rawResp = '';
  if(!practiceConsumed){
    if(!(await decUses()))return;
    const firstResp = await getPaidAIResponse('practice_q', prompt);
    if(!firstResp){
      practiceConsumed=false;
      hideInput();
      addBtns([
        {label:'Попробовать ещё раз',action:'runDrill()'},
        {label:'🏠 На главную',action:'showHome()'},
      ]);
      return;
    }
    practiceConsumed=true;
    CS.sessionCount++;
    saveCS();
    rawResp = firstResp;
  } else {
    showTyping();
    rawResp = await callAI('practice_q', prompt);
    hideTyping();
    if(isAIErrorResponse(rawResp)){
      aiMsg('Не удалось получить новый вопрос. Попробуйте ещё раз без дополнительного списания.');
      hideInput();
      addBtns([
        {label:'Попробовать ещё раз',action:'runDrill()'},
        {label:'🏠 На главную',action:'showHome()'},
      ]);
      return;
    }
  }

  let resp = sanitizeModuleResponse('practice_q', rawResp, contextBlock);
  const duplicatePracticeQuestion = practiceQuestions.some(question =>
    normalizeComparableText(question) === normalizeComparableText(resp)
  );
  if(duplicatePracticeQuestion){
    showTyping();
    const retryResp = await callAI('practice_q', `${prompt}\n\nПредыдущий вариант совпал с уже заданным вопросом. Сформулируй другой вопрос.`);
    hideTyping();
    if(!isAIErrorResponse(retryResp)){
      resp = sanitizeModuleResponse('practice_q', retryResp, contextBlock);
    }
  }

  practiceRound = nextRound;
  practiceQuestion=resp;
  practiceQuestions.push(resp);
  const roundLabel = practiceRound === 1 ? 'Разминка' : `Раунд ${practiceRound - 1}`;
  aiMsg(`**${roundLabel}**\n\n${resp}`);
  showInput('Ваш ответ... (или используйте микрофон)');
  persistPracticeProgress('PRACTICE_ANS');
}

function gotAnswer(text){
  if(practiceRound===1){
    practiceAnswer = trimInput(text, 800, 'practice_warmup.answer');
    appState='PRACTICE_WARMUP_DONE';
    hideInput();
    aiMsg('Разминка пройдена. Теперь можно перейти к первому оцениваемому раунду.');
    addBtns([
      {label:'Первый оцениваемый раунд',action:'runDrill()'},
      {label:'Начать заново',action:"restartModule('practice')"},
    ]);
    persistPracticeProgress('PRACTICE_WARMUP_DONE');
    return;
  }
  practiceAnswer = trimInput(text, 800, 'practice_score.answer');
  appState='PRACTICE_SELF';
  aiMsg('Оцените свой ответ по 5 пунктам (1-5 каждый):\n\n**Содержание** — глубина, детали, цифры\n**Структура** — понятный ход ответа\n**Попадание в тему** — насколько точно вы ответили\n**Убедительность** — насколько вам верят\n**Уникальность** — чем ваш ответ запоминается\n\nВведите 5 цифр через пробел, например: 3 4 3 2 3');
  showInput('5 оценок через пробел...');
  persistPracticeProgress('PRACTICE_SELF');
}

async function runPracticeScore(selfScores){
  appState='PRACTICE_SCORED';
  hideInput();
  showTyping();
  const candidateAnswer = practiceAnswer;
  const rawResp=await callAI('practice_score',`Оцени ответ кандидата. Роль: ${CS.profile.role}. Вопрос: ${practiceQuestion}. Ответ кандидата: ${candidateAnswer}. Самооценка кандидата: ${selfScores}. Дай оценку по 5 пунктам (Содержание, Структура, Попадание в тему, Убедительность, Уникальность) от 1 до 5. Сравни с самооценкой. Формат: Название: X/5. Не используй markdown и не заканчивай ответ вопросом.`);
  hideTyping();
  if(isAIErrorResponse(rawResp)){
    appState='PRACTICE_SELF';
    aiMsg('Не удалось получить разбор ответа. Попробуйте ещё раз без дополнительного списания.');
    showInput('5 оценок через пробел...');
    persistPracticeProgress('PRACTICE_SELF');
    return;
  }
  const resp = sanitizeModuleResponse('practice_score', rawResp, `${practiceQuestion}\n${candidateAnswer}`);
  aiMsg(resp);

  updateDrillProgression(resp);

  if(currentUser.plan == null && practiceRound >= 3){
    addBtns([{label:'🏠 На главную',action:'showHome()'}]);
    showUpgrade();
    persistPracticeProgress('PRACTICE_SCORED');
    return;
  }

  addBtns([
    {label:'Следующий раунд',action:'runDrill()'},
    {label:'Начать заново',action:"restartModule('practice')"},
    {label:'🏠 На главную',action:'showHome()'},
  ]);
  persistPracticeProgress('PRACTICE_SCORED');
}

function updateDrillProgression(debrief){
  const scores=extract5(debrief);
  if(scores&&scores.every(s=>s>=3)&&practiceRound>=3){
    CS.drillStage=Math.min(8,(CS.drillStage||1)+1);
    saveCS();
    aiMsg(`🎉 **Уровень пройден!** Вы перешли на уровень ${CS.drillStage}: ${DRILL_NAMES[CS.drillStage]}`);
    updateSidebar();
  }
  if(scores){
    CS.scoreHistory.push({round:practiceRound,stage:CS.drillStage,scores,date:Date.now()});
    if(CS.scoreHistory.length > 50){
      CS.scoreHistory = CS.scoreHistory.slice(-50);
    }
  }
  saveCS();
}

function extract5(text){
  const scores=[];
  for(const dim of SCORE_DIMENSIONS){
    const score=findDimensionScore(text, dim.labels);
    if(score!==null)scores.push(score);
  }
  return scores.length===5?scores:null;
}

// ====== MOCK ======
function startMock(){
  setHeader('Пробное интервью','Пробное интервью');
  if(CS.mockInProgress && CS.mockInProgress.index > 0){
    mockQuestions = Array.isArray(CS.mockInProgress.questions) ? [...CS.mockInProgress.questions] : [];
    mockAnswers = Array.isArray(CS.mockInProgress.answers) ? [...CS.mockInProgress.answers] : [];
    mockIndex = Number.isFinite(CS.mockInProgress.index) ? CS.mockInProgress.index : 0;
    aiMsg('Найдено незавершённое пробное интервью. Продолжить?');
    addBtns([
      {label:'Продолжить', action:'resumeMock()'},
      {label:'Начать заново', action:'clearAndStartMock()'},
    ]);
    hideInput();
    return;
  }
  clearAndStartMock(true);
}

function clearAndStartMock(skipRestart = false){
  mockQuestions=[];
  mockAnswers=[];
  mockIndex=0;
  archiveCurrentChatLog('mock');
  clearStoredChatLog('mock');
  delete CS.chatDrafts.mock;
  delete CS.mockInProgress;
  saveCS();
  if(skipRestart){
    aiMsg('**Пробное интервью** — имитация реального собеседования.\n\n4 вопроса подряд, без подсказок между ними. Полный разбор с итоговой оценкой будет только в конце.\n\nГотовы?');
    addBtns([{label:'Начать интервью',action:'askMQ()'}]);
    hideInput();
    return;
  }
  startMock();
}

function resumeMock(){
  mockQuestions = Array.isArray(CS.mockInProgress?.questions) ? [...CS.mockInProgress.questions] : [];
  mockAnswers = Array.isArray(CS.mockInProgress?.answers) ? [...CS.mockInProgress.answers] : [];
  mockIndex = Number.isFinite(CS.mockInProgress?.index) ? CS.mockInProgress.index : 0;

  if(mockIndex >= 4 && mockAnswers.length >= 4){
    appState='MOCK_SELF';
    aiMsg('Интервью завершено! Оцените себя по 5 пунктам (1-5 каждый):\nСодержание Структура Попадание в тему Убедительность Уникальность\n\nВведите 5 цифр через пробел:');
    showInput('5 оценок через пробел...');
    return;
  }

  if(mockQuestions.length > mockAnswers.length){
    appState='MOCK_ANS';
    aiMsg(`**Вопрос ${mockIndex}/4:**\n\n${mockQuestions[mockQuestions.length - 1]}`);
    showInput('Ваш ответ...');
    return;
  }

  askMQ();
}

function persistMockProgress(){
  CS.mockInProgress = {
    questions:[...mockQuestions],
    answers:[...mockAnswers],
    index:mockIndex,
    billed:Boolean(CS.mockInProgress?.billed)
  };
  saveCS();
  saveChatLog('mock');
}

function showMockSelfInput(){
  appState='MOCK_SELF';
  aiMsg('Интервью завершено! Оцените себя по 5 пунктам (1-5 каждый):\nСодержание Структура Попадание в тему Убедительность Уникальность\n\nВведите 5 цифр через пробел:');
  showInput('5 оценок через пробел...');
  saveChatLog('mock');
}

async function askMQ(){
  const nextIndex = mockIndex + 1;
  if(nextIndex > 4){
    showMockSelfInput();
    return;
  }
  appState='MOCK_ANS';
  const histText = trimInputNicely(CS.profile.hist || '', 400, 'mock.hist');
  const jdText = trimInputNicely(CS.lastJD || '', 700, 'mock.jd');
  const storiesText = CS.storybank.length > 0
    ? CS.storybank.slice(0, 2).map((story, index) => `${index + 1}. ${trimInputNicely(story.raw || '', 150, 'mock.story')}`).join('\n')
    : '';
  const prompt = `Задай вопрос ${nextIndex} из 4 для пробного интервью. Роль: ${CS.profile.role}. Трек: ${CS.profile.track}. Опыт: ${histText || 'не указан'}. ${jdText ? `Контекст вакансии: ${jdText}.` : ''} ${storiesText ? `Реальные истории кандидата:\n${storiesText}\n` : ''} Предыдущие вопросы: ${mockQuestions.join('; ') || 'пока нет'}. Вопрос должен быть новым, конкретным, без markdown и без повторов.`;

  let rawResp = '';
  if(!Boolean(CS.mockInProgress?.billed)){
    if(!(await decUses()))return;
    CS.mockInProgress = {
      questions:[...mockQuestions],
      answers:[...mockAnswers],
      index:mockIndex,
      billed:true
    };
    saveCS();
    const firstResp = await getPaidAIResponse('mock_q', prompt);
    if(!firstResp){
      if(CS.mockInProgress){
        CS.mockInProgress.billed = false;
        saveCS();
      }
      hideInput();
      addBtns([
        {label:'Попробовать ещё раз',action:'askMQ()'},
        {label:'🏠 На главную',action:'showHome()'},
      ]);
      return;
    }
    CS.sessionCount++;
    saveCS();
    rawResp = firstResp;
  } else {
    showTyping();
    rawResp = await callAI('mock_q', prompt);
    hideTyping();
    if(isAIErrorResponse(rawResp)){
      aiMsg('Не удалось получить следующий вопрос интервью. Попробуйте ещё раз без дополнительного списания.');
      hideInput();
      addBtns([
        {label:'Попробовать ещё раз',action:'askMQ()'},
        {label:'🏠 На главную',action:'showHome()'},
      ]);
      return;
    }
  }

  let resp = sanitizeModuleResponse('mock_q', rawResp, `${histText}\n${jdText}\n${storiesText}`);
  const duplicateMockQuestion = mockQuestions.some(question =>
    normalizeComparableText(question) === normalizeComparableText(resp)
  );
  if(duplicateMockQuestion){
    showTyping();
    const retryResp = await callAI('mock_q', `${prompt}\n\nПредыдущий вариант повторяет уже заданный вопрос. Сформулируй другой вопрос.`);
    hideTyping();
    if(!isAIErrorResponse(retryResp)){
      resp = sanitizeModuleResponse('mock_q', retryResp, `${histText}\n${jdText}\n${storiesText}`);
    }
  }

  mockIndex = nextIndex;
  mockQuestions.push(resp);
  persistMockProgress();
  aiMsg(`**Вопрос ${mockIndex}/4:**\n\n${resp}`);
  showInput('Ваш ответ...');
  saveChatLog('mock');
}

function gotMockAns(text){
  mockAnswers.push(trimInput(text, 500, 'mock.answer'));
  persistMockProgress();
  askMQ();
}

async function runMockDebrief(selfScores){
  appState='MOCK_DONE';
  hideInput();
  let qa='';
  mockQuestions.forEach((q,i)=>{
    const ans = trimInput(mockAnswers[i] || 'нет', 500, 'mock_debrief.answer');
    qa += `Вопрос ${i+1}: ${q}\nОтвет: ${ans}\n\n`;
  });
  showTyping();
  const rawResp=await callAI('mock_debrief',`Полный разбор пробного интервью. Роль: ${CS.profile.role}. Самооценка кандидата по интервью целиком: ${selfScores}.\n\n${qa}\n\nДай оценку по 5 пунктам (Содержание, Структура, Попадание в тему, Убедительность, Уникальность), дай итоговую оценку интервью (Однозначно берём/Берём/Сомнения/Не берём), сравни с самооценкой. Не распределяй самооценку по отдельным вопросам и не повторяй её в каждом блоке. Не используй markdown и не заканчивай ответ вопросом.`);
  hideTyping();
  const resp = isAIErrorResponse(rawResp) ? null : sanitizeModuleResponse('mock_debrief', rawResp, qa);
  if(!resp){
    appState='MOCK_SELF';
    aiMsg('Не удалось получить полный разбор интервью. Попробуйте ещё раз без дополнительного списания.');
    showInput('5 оценок через пробел...');
    return;
  }
  aiMsg(resp);
  pushAssistantToHistory(resp, 'mock_debrief.assistant');
  delete CS.mockInProgress;
  saveCS();
  addBtns([
    {label:'Ещё раз',action:"startModule('mock')"},
    {label:'🏠 На главную',action:'showHome()'},
  ]);
}

// ====== PREP ======
function startPrep(){
  setHeader('Подготовка к собеседованию','Вероятные вопросы');
  appState='PREP';
  aiMsg('Вставьте описание вакансии или компании — я подготовлю план собеседования с вероятными вопросами.');
  showInput('Описание вакансии или компании...');
}

async function runPrep(text){
  if(!(await decUses()))return;
  appState='PREP_DONE';
  hideInput();
  const prepText = trimInput(text, 2000, 'prep');
  const prepHistText = trimInput(CS.profile.hist || '', 400, 'prep.hist');
  rememberModuleInput('prep', `${CS.profile.role || ''}\n${prepHistText}\n${prepText}`);
  setDialogFollowupCount('prep', 0);
  setDialogModule('prep', 'prep');
  const rawResp=await getPaidAIResponse('prep',`Создай план подготовки к собеседованию с вероятными вопросами. Роль: ${CS.profile.role}. Опыт: ${prepHistText}. Вакансия/компания: ${prepText}. Не выдавай предположения о процессе интервью конкретной компании как подтверждённые факты. Если чего-то нет во входе, используй формулировки "возможно", "стоит уточнить у рекрутера". Не используй markdown и не заканчивай ответ вопросом.`);
  const resp = rawResp ? sanitizeModuleResponse('prep', rawResp, getModuleInput('prep')) : null;
  if(!resp){
    appState='PREP';
    showInput('Описание вакансии или компании...');
    return;
  }
  aiMsg(resp);
  pushAssistantToHistory(resp, 'prep.assistant');
  addBtns([
    {label:'💪 Потренироваться на этих вопросах',action:"startModule('practice')"},
    {label:'🏠 На главную',action:'showHome()'},
  ]);
  showInput(`Уточняющий вопрос (осталось ${DIALOG_MAX_FOLLOWUPS} из ${DIALOG_MAX_FOLLOWUPS})...`);
}

// ====== CONCERNS ======
function startConcerns(){
  setHeader('Возражения рекрутера','Ответы на возражения');
  appState='CONCERNS_SELF';
  aiMsg('Какие **возражения** или **сомнения** рекрутер мог бы иметь по вашей кандидатуре? Напишите всё, что приходит в голову — потом я добавлю пропущенное и подскажу, как отвечать.');
  showInput('Ваши предположения о возражениях рекрутера...');
}

async function runConcerns(text){
  if(!(await decUses()))return;
  appState='CONCERNS_DONE';
  hideInput();
  const concernsText = trimInput(text, 1000, 'concerns');
  const concernsHistText = trimInput(CS.profile.hist || '', 400, 'concerns.hist');
  rememberModuleInput('concerns', `${CS.profile.role || ''}\n${concernsHistText}\n${concernsText}`);
  setDialogFollowupCount('concerns', 0);
  setDialogModule('concerns', 'concerns');
  const rawResp=await getPaidAIResponse('concerns',`Кандидат думает, что рекрутер может возразить: ${concernsText}. Роль: ${CS.profile.role}. Опыт: ${concernsHistText}. Сначала подтверди, что кандидат определил верно, потом добавь пропущенные возражения и дай ответы на каждое. Пиши обычным текстом без markdown-таблиц, без символов #, >, ---, без служебной разметки. Не заканчивай ответ вопросом.`);
  const resp = rawResp ? sanitizeModuleResponse('concerns', rawResp, getModuleInput('concerns')) : null;
  if(!resp){
    appState='CONCERNS_SELF';
    showInput('Ваши предположения о возражениях рекрутера...');
    return;
  }
  aiMsg(resp);
  pushAssistantToHistory(resp, 'concerns.assistant');
  addBtns([{label:'🏠 На главную',action:'showHome()'}]);
  showInput(`Уточняющий вопрос (осталось ${DIALOG_MAX_FOLLOWUPS} из ${DIALOG_MAX_FOLLOWUPS})...`);
}

// ====== STORIES ======
function startStories(){
  setHeader('Мои истории','Истории из опыта');
  appState='STORY_ADD';
  const count=CS.storybank.length;
  let msg=`**Мои истории** (${count} историй)\n\n`;
  if(count>0){
    msg+=`Ваши истории:\n`;
    CS.storybank.forEach((s,i)=>{msg+=`${i+1}. **${s.title}** — ${storySkillLabel(s.skill)}\n`});
    msg+=`\nРасскажите новую историю или выберите действие:`;
  } else {
    msg+=`Расскажите любую рабочую историю — я помогу оформить её по схеме: ситуация, задача, действие, результат, и выделю ваш уникальный вывод.`;
  }
  aiMsg(msg);
  showInput('Расскажите историю из рабочего опыта...');
}

async function runAddStory(text){
  if(!(await decUses()))return;
  hideInput();
  const storyText = trimInput(text, 1500, 'story');
  rememberModuleInput('stories', `${CS.profile.role || ''}\n${storyText}`);
  const rawResp=await getPaidAIResponse('story',`Структурируй эту историю по схеме "ситуация, задача, действие, результат", определи полезный навык и сильную сторону кандидата, выдели уникальный вывод. История: ${storyText}. Роль: ${CS.profile.role}. Не заканчивай ответ вопросом и не используй markdown.`);
  const resp = rawResp ? sanitizeModuleResponse('story', rawResp, getModuleInput('stories')) : null;
  if(!resp){
    appState='STORY_ADD';
    showInput('Расскажите историю из рабочего опыта...');
    return;
  }
  aiMsg(resp);

  const title=text.substring(0,40)+'...';
  CS.storybank.push({title,raw:text,structured:resp,skill:'лидерство',strength:'решение проблем'});
  saveCS();

  addBtns([
    {label:'Добавить ещё историю',action:"appState='STORY_ADD';chatHistory=[];showInput('Расскажите историю...')"},
    {label:'🏠 На главную',action:'showHome()'},
  ]);
}

// ====== HYPE ======
function startHype(){
  setHeader('Настрой перед собесом','Настрой перед собесом');
  appState='HYPE';
  aiMsg('**Настрой перед собесом**\n\nКогда собеседование и как вы себя чувствуете?');
  addBtns([
    {label:'Нервничаю, собес скоро',action:"runHype('Нервничаю, собеседование скоро')"},
    {label:'Нужна уверенность',action:"runHype('Нужно поднять уверенность в себе')"},
    {label:'Боюсь сложных вопросов',action:"runHype('Боюсь что не отвечу на сложные вопросы')"},
  ]);
  showInput('Или опишите своё состояние...');
}

async function runHype(text){
  if(!(await decUses()))return;
  appState='HYPE_DONE';
  hideInput();
  const hypeText = trimInput(text, 500, 'hype');
  const hypeHistText = trimInput(CS.profile.hist || '', 300, 'hype.hist');
  rememberModuleInput('hype', `${CS.profile.role || ''}\n${hypeHistText}\n${hypeText}`);
  const rawResp=await getPaidAIResponse('hype',`Помоги настроиться перед собеседованием. Роль: ${CS.profile.role}. Опыт: ${hypeHistText}. Состояние: ${hypeText}. Дай мотивацию, напомни о сильных сторонах, дай конкретные техники управления стрессом. Не представляй спорные детали профиля как подтверждённые факты: если не хватает подтверждения, говори общо и безопасно. Не используй markdown и не заканчивай ответ вопросом.`);
  const resp = rawResp ? sanitizeModuleResponse('hype', rawResp, getModuleInput('hype')) : null;
  if(!resp){
    appState='HYPE';
    showInput('Или опишите своё состояние...');
    return;
  }
  aiMsg(resp);
  addBtns([{label:'🏠 На главную',action:'showHome()'}]);
}

// ====== SALARY ======
function startSalary(){
  setHeader('Зарплатные ожидания','Как называть сумму');
  appState='SALARY';
  aiMsg('**Как называть сумму**\n\nРасскажите:\n- Какие данные о зарплатах вы нашли (hh.ru, сайты с зарплатами, чаты)?\n- Какая у вас текущая или последняя зарплата?\n- Какой диапазон вы хотите?\n\n*Я не придумываю рыночные цифры — только помогаю интерпретировать ваши данные.*');
  showInput('Ваши данные о зарплатах...');
}

async function runSalary(text){
  if(!(await decUses()))return;
  appState='SALARY_DONE';
  hideInput();
  const salaryText = trimInput(text, 1000, 'salary');
  rememberModuleInput('salary', salaryText);
  setDialogFollowupCount('salary', 0);
  setDialogModule('salary', 'salary');
  const rawResp=await getPaidAIResponse('salary',`Помоги с зарплатной стратегией. НЕ ПРИДУМЫВАЙ рыночные цифры. Используй только денежные цифры, проценты и сроки, которые есть во входных данных кандидата. Не добавляй новые диапазоны, новые сроки пересмотра и новые условия. Роль: ${CS.profile.role}. Данные кандидата: ${salaryText}. Не используй markdown и не заканчивай ответ вопросом.`);
  const resp = rawResp ? sanitizeModuleResponse('salary', rawResp, salaryText) : null;
  if(!resp){
    appState='SALARY';
    showInput('Ваши данные о зарплатах...');
    return;
  }
  aiMsg(resp);
  pushAssistantToHistory(resp, 'salary.assistant');
  addBtns([
    {label:'🤝 Переговоры по офферу',action:"startModule('negotiate')"},
    {label:'🏠 На главную',action:'showHome()'},
  ]);
  showInput(`Уточняющий вопрос (осталось ${DIALOG_MAX_FOLLOWUPS} из ${DIALOG_MAX_FOLLOWUPS})...`);
}

// ====== NEGOTIATE ======
function startNegotiate(){
  setHeader('Переговоры по офферу','Переговоры по офферу');
  appState='NEGOTIATE';
  aiMsg('**Переговоры по офферу**\n\nОпишите полученный оффер:\n- Компания и роль\n- Предложенная зарплата\n- Бонусы, опционы, дополнительные условия\n- Что хотите улучшить\n\n*Для вопросов про долю в компании, налоги и документы о неразглашении лучше отдельно поговорить с юристом или финансовым консультантом.*');
  showInput('Опишите ваш оффер...');
}

async function runNegotiate(text){
  if(!(await decUses()))return;
  appState='NEGOTIATE_DONE';
  hideInput();
  const negotiateText = trimInput(text, 1500, 'negotiate');
  rememberModuleInput('negotiate', negotiateText);
  setDialogFollowupCount('negotiate', 0);
  setDialogModule('negotiate', 'negotiate');
  const rawResp=await getPaidAIResponse('negotiate',`Помоги с переговорами по офферу. НЕ ПРИДУМЫВАЙ рыночные цифры. Используй только денежные цифры, проценты, сроки и условия, которые есть во входных данных кандидата. Для вопросов про долю в компании, налоги и документы о неразглашении советуй юриста. Роль: ${CS.profile.role}. Оффер: ${negotiateText}. Не используй markdown и не заканчивай ответ вопросом.`);
  const resp = rawResp ? sanitizeModuleResponse('negotiate', rawResp, negotiateText) : null;
  if(!resp){
    appState='NEGOTIATE';
    showInput('Опишите ваш оффер...');
    return;
  }
  aiMsg(resp);
  pushAssistantToHistory(resp, 'negotiate.assistant');
  addBtns([{label:'🏠 На главную',action:'showHome()'}]);
  showInput(`Уточняющий вопрос (осталось ${DIALOG_MAX_FOLLOWUPS} из ${DIALOG_MAX_FOLLOWUPS})...`);
}

// ====== UPGRADE OVERLAY ======
function showUpgrade(){
  const overlay=document.getElementById('upgradeOverlay');
  overlay.classList.add('show');
  document.getElementById('upgradeTitle').textContent='Продолжайте с тем тарифом, который подходит вашему этапу';
  document.getElementById('upgradeDesc').textContent='Разовая оплата, не подписка. Прогресс сохранится при переходе на следующий тариф.';
  document.getElementById('upgradeContent').innerHTML=`
    <div class="upgrade-session-explainer">
      <div>
        <strong>Что такое сессия</strong>
        <span>Один законченный шаг внутри подготовки: разбор резюме, тренировочный вопрос, пробное интервью или подготовка к переговорам.</span>
      </div>
      <div class="upgrade-chip-list">
        <span>Без подписки</span>
        <span>Прогресс сохранится</span>
        <span>Выбор по сценарию</span>
      </div>
    </div>
    <div class="upgrade-grid">
      <div class="price-card">
        <h3>Старт</h3>
        <div class="price">490 ₽</div>
        <div class="price-note">Разовая оплата</div>
        <p class="upgrade-summary">Если нужно быстро понять, где ответ теряет силу, и собрать базу для дальнейшей подготовки.</p>
        <p class="upgrade-target">Подходит, если собеседование ещё не завтра, но уже пора заняться резюме, вакансией и первыми ответами.</p>
        <ul class="price-features">
          <li>Разбор резюме</li>
          <li>Разбор вакансии</li>
          <li>Рассказ о себе</li>
          <li>Первые тренировочные вопросы</li>
          <li>8 сессий</li>
        </ul>
        <div class="upgrade-outcome">
          <strong>К концу</strong>
          <p>Есть понятная база, на которой уже можно тренировать интервью без лишней суеты.</p>
        </div>
        <button class="price-btn secondary" onclick="buy('start', 490, 8)">Начать со Старт</button>
      </div>
      <div class="price-card popular">
        <div class="price-badge">Рекомендуем</div>
        <h3>Подготовка</h3>
        <div class="price">990 ₽</div>
        <div class="price-note">Разовая оплата</div>
        <p class="upgrade-summary">Если интервью уже близко и нужен полный тренировочный цикл, а не только разбор текста.</p>
        <p class="upgrade-target">Подходит, если хотите пройти путь от первых ответов до пробного интервью и увидеть, где ещё сыпетесь под давлением.</p>
        <ul class="price-features">
          <li>Всё из Старта</li>
          <li>8 уровней тренировок и голос</li>
          <li>Пробное интервью с итоговым разбором</li>
          <li>Истории из опыта и ответы на возражения</li>
          <li>25 сессий</li>
        </ul>
        <div class="upgrade-outcome">
          <strong>К концу</strong>
          <p>Есть несколько прогонов, понятные слабые места и план финальных правок перед реальным интервью.</p>
        </div>
        <button class="price-btn primary" onclick="buy('prep', 990, 25)">Выбрать Подготовку</button>
      </div>
      <div class="price-card">
        <h3>Оффер</h3>
        <div class="price">1990 ₽</div>
        <div class="price-note">Разовая оплата</div>
        <p class="upgrade-summary">Если не хотите остановиться после интервью и хотите спокойно пройти разговор о деньгах и условиях.</p>
        <p class="upgrade-target">Подходит, если оффер уже есть или финальные этапы близко и нужна позиция, а не импровизация на звонке.</p>
        <ul class="price-features">
          <li>Всё из Подготовки</li>
          <li>Зарплатные ожидания</li>
          <li>Переговоры по офферу</li>
          <li>Фразы для разговора с рекрутером</li>
          <li>40 сессий</li>
        </ul>
        <div class="upgrade-outcome">
          <strong>К концу</strong>
          <p>Есть аргументы, рамки уступок и формулировки для разговора об оффере без лишнего пафоса.</p>
        </div>
        <button class="price-btn secondary" onclick="buy('offer', 1990, 40)">Перейти к Офферу</button>
      </div>
    </div>
    <div class="upgrade-footnote">
      Одна тренировка, один разбор или одно пробное интервью считаются отдельными сессиями.
    </div>
  `;
}

function hideOverlay(){
  document.getElementById('upgradeOverlay').classList.remove('show');
}

async function buy(plan,price,uses){
  void price;
  void uses;
  try{
    const data = await requestJSON(`${API_BASE}/payment.php`,{
      method:'POST',
      body:JSON.stringify({action:'create',plan})
    });
    if(data.confirmation_url){
      window.location.href = data.confirmation_url;
      return;
    }
    aiMsg('Не удалось открыть страницу оплаты.');
  }catch(err){
    aiMsg(err.message || 'Не удалось начать оплату. Попробуйте позже.');
  }
}

function doBuy(){
  const plan=currentUser.plan;
  if(!plan||plan==='start') buy('prep',990,25);
  else if(plan==='prep') buy('offer',1990,40);
}

// ====== AI CALL ======
async function callAI(module, prompt){
  isProcessing=true;
  document.getElementById('sendBtn').disabled=true;

  try{
    const TOKEN_LIMITS = {
      decode: 1800,
      resume_quick: 1000,
      resume_standard: 2000,
      resume_deep: 2800,
      pitch: 1600,
      practice_q: 450,
      practice_score: 1500,
      mock_q: 450,
      mock_debrief: 2800,
      prep: 2200,
      concerns: 1800,
      story: 1200,
      hype: 1200,
      salary: 1800,
      negotiate: 1800,
      kickoff_analysis: 600,
    };
    const ONE_SHOT = [
      'hype', 'kickoff_analysis'
    ];
    const SHORT_HIST = [
      'story', 'practice_score', 'mock_debrief',
      'decode', 'resume_quick', 'resume_standard', 'resume_deep',
      'pitch', 'prep', 'concerns', 'salary', 'negotiate'
    ];
    const maxTok = TOKEN_LIMITS[module] || 1000;
    const hist = ONE_SHOT.includes(module)
      ? []
      : SHORT_HIST.includes(module)
        ? chatHistory.slice(-6)
        : chatHistory.slice(-6);
    const sysPrompt=getSystemPrompt(module);
    const msgs=[{role:'system',content:sysPrompt},...hist,{role:'user',content:prompt}];
    const data=await requestJSON(`${API_BASE}/ai.php`,{
      method:'POST',
      body:JSON.stringify({module,max_tokens:maxTok,messages:msgs})
    });
    return data.content || data.choices?.[0]?.message?.content || 'Ошибка API';
  }catch(e){
    if(e.status===401){
      showAuth(DEFAULT_AUTH_MODE);
      return 'Сессия истекла. Войдите снова.';
    }
    if(e.status===403 && e.data && e.data.code === 'email_not_verified'){
      pendingVerificationEmail = (currentUser && currentUser.email) || pendingVerificationEmail;
      showAuth('verify_email');
      showAuthInfo('Для использования сервиса подтвердите email. Проверьте письмо и папку "Спам".');
      return 'Email не подтверждён.';
    }
    if(e.status===403){
      showUpgrade();
      return e.message || 'Лимит сессий исчерпан.';
    }
    return e.message || 'Ошибка соединения с API. Проверьте ключ и интернет.';
  }finally{
    isProcessing=false;
    document.getElementById('sendBtn').disabled=false;
  }
}

function getSystemPrompt(module){
  const base = `Ты — OfferAI, AI-тренажёр для подготовки к собеседованиям на российском рынке труда. Твоя задача — помочь кандидату пройти интервью сильнее через практику, честную обратную связь и структурированную подготовку.

ПРАВИЛА РАБОТЫ:
1. Отвечай только по-русски. Никакого English в ответах пользователю.
2. Оценка только по шкале 1-5, никогда не используй шкалу 1-10.
3. Один вопрос за раз — никогда не задавай несколько вопросов одновременно.
4. Сначала всегда отмечай сильные стороны, потом зоны роста.
5. Никогда не придумывай рыночные цифры зарплат — только интерпретируй данные, которые принёс кандидат.
6. Для вопросов про долю в компании, налоги и юридические документы всегда советуй консультацию с юристом или финансовым советником.
7. Будь конкретным — никаких общих фраз, только практические советы, применимые к ситуации кандидата.
8. Если данных не хватает, прямо скажи, что нужно уточнить, но не выдумывай факты.
9. Не пугай кандидата и не обесценивай его опыт: честность должна быть полезной, а не разрушительной.
10. Если модуль просит оценку, всегда опирайся на факты из ответа кандидата, а не на догадки.
11. Никогда не выдумывай достижения, цифры, проценты, сроки, названия компаний, стек, контактные данные, ссылки, технологии, инструменты или условия, которых нет во входных данных.
12. Если для сильной формулировки не хватает факта, используй пометки вида [нужна цифра], [нужен результат], [нужно уточнение], [добавьте технологию], а не подставляй свои данные.
13. Если тебе нужно уточнение от пользователя — задай максимум ОДИН уточняющий вопрос. Никогда не задавай несколько вопросов сразу. Если данных достаточно — давай ответ сразу, не спрашивай лишнего.
14. В модулях резюме категорически запрещено добавлять технологии, фреймворки, языки программирования или инструменты, которых не было в тексте пользователя или его сохранённых данных.
15. В модулях salary и negotiate категорически запрещено называть денежные цифры, проценты, сроки и условия, которых пользователь не упоминал сам.
16. Никогда не представляй предположения как подтверждённые факты. Если не уверен — используй формулировки «возможно», «как правило», «нужно уточнить», «уточните у рекрутера».
17. Никогда не заканчивай ответ вопросом, если не уверен, что у пользователя будет открыто поле ввода для ответа. В таких случаях завершай ответ выводом, рекомендацией или следующим шагом.
18. Не используй markdown-разметку в финальном тексте для пользователя: никаких заголовков с #, таблиц с |, блоков с >, разделителей --- и кодовых блоков.

СТИЛЬ И ФОРМАТ:
- Пиши ясно, структурно, без канцелярита.
- Для рекомендаций предпочитай короткие списки и готовые формулировки.
- Если просишь кандидата что-то сделать, формулируй это как следующий конкретный шаг.
- Для тревожных мест резюме или интервью не драматизируй — показывай, как объяснить ситуацию спокойно и убедительно.
- В мотивационных блоках не используй пустые лозунги: опирайся на сильные стороны и подтверждённый опыт кандидата.

НАЗВАНИЯ ОЦЕНОК (строго по-русски):
- Содержание
- Структура
- Попадание в тему
- Убедительность
- Уникальность

ИТОГОВАЯ ОЦЕНКА ИНТЕРВЬЮ (строго):
- Однозначно берём
- Берём
- Сомнения
- Не берём

	ПОВЕДЕНИЕ ПО МОДУЛЯМ:
	- kickoff_analysis: после знакомства дай короткий персональный стартовый план, без воды и без повторения анкеты слово в слово.
	- decode: раскладывай вакансию по понятным блокам, помечай уверенность, показывай скрытые требования и вопросы рекрутеру.
- resume_*: переписывай формулировки в сильные и конкретные, но только в рамках фактов кандидата; если данных не хватает, ставь [нужна цифра], [нужен результат], [добавьте технологию], [нужно уточнение], а не выдумывай стек, компании, метрики и инструменты.
- pitch: собирай рассказ по логике «что делаю сейчас -> что делал раньше -> куда иду дальше», без воды.
- practice_q и mock_q: задавай только один вопрос за раз и не выдавай разбор раньше времени.
- practice_score и mock_debrief: оценивай строго по 5 измерениям и сравнивай с самооценкой, если она есть; если ответ слабый или общий, объясняй, каких фактов не хватает, а не выдумывай их.
- prep: помогай готовиться к конкретной вакансии или компании, а не выдавай абстрактный список вопросов.
- concerns: сначала подтвердить, какие возражения кандидат определил верно, потом добавить пропущенные и дать спокойные ответы.
- story: собирай историю по схеме «ситуация, задача, действие, результат», выделяй сильную сторону и уникальный вывод.
- hype: помогай успокоиться перед интервью, напоминай о сильных сторонах, давай конкретные техники, а не просто поддержку.
- salary и negotiate: не придумывай рынок, помогай интерпретировать только данные кандидата и формулировать переговорную позицию.

ПРОФИЛЬ КАНДИДАТА:
Роль: ${CS?.profile?.role || 'не указана'}
Направление: ${CS?.profile?.track || 'не указано'}
Опыт: ${trimInput(CS?.profile?.hist || 'не указан', 300, 'sys.hist')}
Таймлайн: ${CS?.profile?.tl || 'не указан'}
Уровень прямоты обратной связи: ${CS?.profile?.dir || '3'}/5`;

  const moduleContext = `\n\nТЕКУЩИЙ РАЗДЕЛ: ${module}`;
  const noQuestionModules = [
    'kickoff_analysis',
    'decode',
    'resume_quick',
    'resume_standard',
    'resume_deep',
    'pitch',
    'practice_score',
    'mock_debrief',
    'prep',
    'concerns',
    'story',
    'hype',
    'salary',
    'negotiate',
  ];
  const moduleSpecificRules = noQuestionModules.includes(module)
    ? '\n\nДОПОЛНИТЕЛЬНОЕ ПРАВИЛО: не задавай вопросов в конце ответа. Завершай ответ выводом или рекомендацией.'
    : '';

  return base + moduleContext + moduleSpecificRules;
}

// ====== VOICE ======
let recognition=null;
let isRecording=false;

function applyVoiceInputLimit(){
  const input = document.getElementById('userInput');
  if(!input) return;
  if(input.value.length > 1200){
    input.value = input.value.substring(0, 1200);
    autoGrow(input);
  }
}

function toggleVoice(){
  if(!('webkitSpeechRecognition' in window)&&!('SpeechRecognition' in window)){
    aiMsg('Голосовой ввод доступен только в Chrome и Safari.');
    return;
  }
  if(isRecording){stopVoice();return;}

  const SR=window.SpeechRecognition||window.webkitSpeechRecognition;
  recognition=new SR();
  recognition.lang='ru-RU';
  recognition.interimResults=true;
  recognition.continuous=true;

  recognition.onresult=(e)=>{
    let transcript='';
    for(let i=e.resultIndex;i<e.results.length;i++){
      transcript+=e.results[i][0].transcript;
    }
    document.getElementById('userInput').value=transcript;
    autoGrow(document.getElementById('userInput'));
  };

  recognition.onerror=()=>{stopVoice()};
  recognition.onend=()=>{
    isRecording=false;
    if(voiceTimer){ clearTimeout(voiceTimer); voiceTimer = null; }
    document.getElementById('voiceBtn').classList.remove('recording');
    applyVoiceInputLimit();
  };

  recognition.start();
  voiceTimer = setTimeout(() => {
    stopVoice();
    aiMsg('Запись остановлена автоматически (60 секунд).');
  }, 60000);
  isRecording=true;
  document.getElementById('voiceBtn').classList.add('recording');
}

function stopVoice(){
  if(voiceTimer){ clearTimeout(voiceTimer); voiceTimer = null; }
  if(recognition){
    const activeRecognition = recognition;
    recognition = null;
    activeRecognition.stop();
  }
  isRecording=false;
  document.getElementById('voiceBtn').classList.remove('recording');
  applyVoiceInputLimit();
}

// ====== INIT ======
function clearAppQueryParams(){
  const url = new URL(window.location.href);
  url.searchParams.delete('auth');
  url.searchParams.delete('payment');
  window.history.replaceState({},'',url.pathname + (url.search ? url.search : ''));
}

async function bootstrapApp(){
  const params = new URLSearchParams(window.location.search);
  const authMode = params.get('auth');
  const paymentSuccess = params.get('payment') === 'success';

  try{
    await refreshCurrentUser();

    if(currentUser && currentUser.emailVerified === false){
      pendingVerificationEmail = currentUser.email || '';
      document.getElementById('app').style.display='none';
      showAuth('verify_email');
      if(authMode || paymentSuccess){
        clearAppQueryParams();
      }
      return;
    }

    await loadCS();
    enterApp();

    if(CS?.profile?.lastModule === 'mock' && Number(CS?.mockInProgress?.index || 0) > 0){
      forceStartModule('mock');
    }

    if(paymentSuccess){
      await refreshCurrentUser();
      updateSidebar();
      showHome();
    }

    if(authMode || paymentSuccess){
      clearAppQueryParams();
    }
  }catch(err){
    document.getElementById('app').style.display='none';
    showAuth(authMode === 'register' ? 'register' : DEFAULT_AUTH_MODE);
  }
}

setupAuthOtpInputs();
window.addEventListener('load',bootstrapApp);
</script>
</body>
</html>
