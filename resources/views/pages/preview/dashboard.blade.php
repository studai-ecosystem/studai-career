{{-- Standalone UI preview — Google / Zoho clean SaaS dashboard. Not wired to real data. --}}
<!DOCTYPE html>
<html lang="en" data-role="student">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<title>Dashboard · StudAI Hire — a StudAI One product</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
:root{
  /* surfaces */
  --bg:#F6F8FC;            /* app canvas (Google grey 50-ish) */
  --side:#FFFFFF;          /* sidebar */
  --card:#FFFFFF;
  --card-2:#F8F9FB;
  --hover:#F1F4F9;
  --active:#E8F0FE;        /* Google blue tint */
  /* lines */
  --line:#E6E8EC;
  --line-2:#DADCE0;
  /* text */
  --txt:#1F2328;           /* grey 900 */
  --txt-2:#5F6368;         /* grey 700 */
  --txt-3:#80868B;         /* grey 600 */
  /* brand blue (matches the tie logo) */
  --pri:#2D6CDF;
  --pri-d:#1B57C4;
  --pri-dd:#143F92;
  --pri-tint:#E8F0FE;
  --pri-soft:#F0F5FF;
  /* status */
  --green:#1E8E3E; --green-t:#E6F4EA;
  --amber:#E37400; --amber-t:#FEF7E0;
  --red:#D93025;  --red-t:#FCE8E6;
  --violet:#7C4DFF; --violet-t:#F3EDFF;
  --teal:#00897B; --teal-t:#E0F2F1;
  /* shape */
  --r:14px; --r-sm:10px; --r-lg:20px; --pill:999px;
  /* elevation (Material-ish) */
  --sh-1:0 1px 2px rgba(60,64,67,.10), 0 1px 3px rgba(60,64,67,.06);
  --sh-2:0 1px 3px rgba(60,64,67,.12), 0 4px 8px rgba(60,64,67,.06);
  --sh-3:0 4px 10px rgba(60,64,67,.14), 0 12px 28px rgba(60,64,67,.10);
  --side-w:248px;
  --font:"Inter",system-ui,-apple-system,"Segoe UI",Roboto,sans-serif;
  --mono:"Roboto Mono",ui-monospace,monospace;
}
*{box-sizing:border-box;margin:0;padding:0}
html,body{height:100%}
body{
  font-family:var(--font);background:var(--bg);color:var(--txt);
  -webkit-font-smoothing:antialiased;text-rendering:optimizeLegibility;
  font-size:14px;line-height:1.5;
}
a{color:inherit;text-decoration:none}
::selection{background:var(--pri-tint)}
::-webkit-scrollbar{width:10px;height:10px}
::-webkit-scrollbar-thumb{background:#D4D7DC;border-radius:8px;border:3px solid var(--bg)}
::-webkit-scrollbar-thumb:hover{background:#BCC0C6}

/* preview ribbon */
.flag{position:fixed;top:0;left:0;right:0;height:30px;z-index:120;
  background:#0F1B33;color:#cdd8f0;display:flex;align-items:center;justify-content:center;
  gap:10px;font-size:11.5px;letter-spacing:.04em;font-weight:600}
.flag b{color:#fff}
.flag .dot{width:6px;height:6px;border-radius:50%;background:#56d28a;box-shadow:0 0 0 3px rgba(86,210,138,.18)}

/* ---------- shell ---------- */
.app{display:grid;grid-template-columns:var(--side-w) 1fr;min-height:100%;padding-top:30px}

/* ---------- sidebar ---------- */
.side{position:sticky;top:30px;height:calc(100vh - 30px);background:var(--side);
  border-right:1px solid var(--line);display:flex;flex-direction:column;padding:18px 14px 14px}
.brand{display:flex;align-items:center;gap:10px;padding:4px 8px 16px}
.brand .mark{width:40px;height:40px;border-radius:11px;background:#fff;border:1px solid var(--line);
  box-shadow:var(--sh-1);display:grid;place-items:center;flex:0 0 40px;overflow:hidden}
.brand .mark svg{width:18px;height:28px;display:block}
.wordmark{display:inline-flex;align-items:flex-end;gap:1px;font-weight:800;font-size:23px;
  letter-spacing:-.04em;color:var(--txt);line-height:1}
.wordmark .tie{width:11px;height:30px;margin:0 .5px -1px;display:block}
.brand .sub{font-size:10.5px;color:var(--txt-3);font-weight:600;letter-spacing:.02em;margin-top:3px}

/* role segmented */
.roleseg{display:flex;background:var(--card-2);border:1px solid var(--line);border-radius:var(--pill);
  padding:4px;margin:4px 6px 16px;gap:4px}
.roleseg button{flex:1;border:0;background:transparent;cursor:pointer;font-family:inherit;
  font-size:12.5px;font-weight:600;color:var(--txt-2);padding:7px 8px;border-radius:var(--pill);
  display:flex;align-items:center;justify-content:center;gap:6px;transition:.16s}
.roleseg button svg{width:15px;height:15px}
.roleseg button:hover{color:var(--txt)}
html[data-role="student"] .roleseg .r-st,
html[data-role="company"] .roleseg .r-co{background:var(--pri);color:#fff;box-shadow:0 1px 2px rgba(45,108,223,.4)}

.nav{flex:1;overflow-y:auto;display:flex;flex-direction:column;gap:2px;padding:0 2px}
.nav::-webkit-scrollbar{width:0}
.navlbl{font-size:10.5px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;
  color:var(--txt-3);padding:14px 12px 6px}
.navitem{display:flex;align-items:center;gap:12px;padding:9px 12px;border-radius:var(--r-sm);
  color:var(--txt-2);font-weight:500;font-size:13.5px;cursor:pointer;position:relative;transition:.14s}
.navitem svg{width:18px;height:18px;flex:0 0 18px;stroke-width:1.9}
.navitem:hover{background:var(--hover);color:var(--txt)}
.navitem.on{background:var(--active);color:var(--pri-d);font-weight:600}
.navitem.on svg{color:var(--pri)}
.navitem .badge{margin-left:auto;font-size:10.5px;font-weight:700;background:var(--pri);color:#fff;
  min-width:18px;height:18px;padding:0 5px;border-radius:9px;display:grid;place-items:center}
.navitem .badge.soft{background:var(--amber-t);color:var(--amber)}
.role-only{display:none}
html[data-role="student"] .role-only.student{display:flex}
html[data-role="company"] .role-only.company{display:flex}
.navlbl.role-only{display:none}
html[data-role="student"] .navlbl.role-only.student{display:block}
html[data-role="company"] .navlbl.role-only.company{display:block}

/* upgrade card */
.promo{margin:10px 4px 8px;background:var(--pri-soft);border:1px solid #D6E4FF;border-radius:var(--r);
  padding:14px}
.promo h5{font-size:12.5px;font-weight:700;color:var(--pri-dd);display:flex;align-items:center;gap:7px}
.promo p{font-size:11.5px;color:var(--txt-2);margin:5px 0 10px;line-height:1.45}
.promo button{width:100%;border:0;background:var(--pri);color:#fff;font-family:inherit;font-weight:600;
  font-size:12px;padding:8px;border-radius:var(--r-sm);cursor:pointer;transition:.16s}
.promo button:hover{background:var(--pri-d)}

.usercard{display:flex;align-items:center;gap:10px;padding:9px 10px;border-radius:var(--r-sm);
  cursor:pointer;border:1px solid var(--line);background:var(--card)}
.usercard:hover{background:var(--hover)}
.usercard .av{width:32px;height:32px;border-radius:50%;flex:0 0 32px;display:grid;place-items:center;
  font-weight:700;font-size:12.5px;color:#fff;background:var(--pri)}
.usercard .nm{font-size:12.5px;font-weight:600;line-height:1.25}
.usercard .pl{font-size:11px;color:var(--txt-3)}
.usercard .chev{margin-left:auto;color:var(--txt-3)}

/* ---------- topbar ---------- */
.main{display:flex;flex-direction:column;min-width:0}
.top{position:sticky;top:30px;z-index:40;background:rgba(246,248,252,.85);backdrop-filter:blur(10px);
  border-bottom:1px solid var(--line);padding:12px 28px;display:flex;align-items:center;gap:18px}
.crumb{display:flex;align-items:center;gap:8px;font-size:13px;color:var(--txt-3);font-weight:500}
.crumb b{color:var(--txt);font-weight:700}
.crumb svg{width:14px;height:14px}
.search{flex:1;max-width:520px;margin:0 auto 0 8px;position:relative}
.search input{width:100%;border:1px solid var(--line-2);background:var(--card);border-radius:var(--pill);
  padding:10px 16px 10px 42px;font-family:inherit;font-size:13.5px;color:var(--txt);transition:.16s;box-shadow:var(--sh-1)}
.search input::placeholder{color:var(--txt-3)}
.search input:focus{outline:none;border-color:var(--pri);box-shadow:0 0 0 3px var(--pri-tint)}
.search svg{position:absolute;left:15px;top:50%;transform:translateY(-50%);width:17px;height:17px;color:var(--txt-3)}
.search kbd{position:absolute;right:12px;top:50%;transform:translateY(-50%);font-family:var(--mono);
  font-size:10.5px;color:var(--txt-3);background:var(--card-2);border:1px solid var(--line);
  border-radius:6px;padding:2px 6px}
.ttools{display:flex;align-items:center;gap:8px;margin-left:auto}
.iconbtn{width:38px;height:38px;border-radius:50%;border:1px solid transparent;background:transparent;
  display:grid;place-items:center;cursor:pointer;color:var(--txt-2);position:relative;transition:.14s}
.iconbtn:hover{background:var(--hover);color:var(--txt)}
.iconbtn svg{width:19px;height:19px}
.iconbtn .ping{position:absolute;top:8px;right:9px;width:7px;height:7px;border-radius:50%;background:var(--red);
  border:2px solid var(--bg)}
.newbtn{display:flex;align-items:center;gap:8px;background:var(--pri);color:#fff;border:0;font-family:inherit;
  font-weight:600;font-size:13px;padding:9px 16px;border-radius:var(--pill);cursor:pointer;
  box-shadow:0 1px 2px rgba(45,108,223,.4);transition:.16s}
.newbtn:hover{background:var(--pri-d);box-shadow:0 2px 6px rgba(45,108,223,.45)}
.newbtn svg{width:17px;height:17px}

/* ---------- content ---------- */
.content{padding:24px 28px 120px;max-width:1320px;width:100%;margin:0 auto}
.hero{display:flex;align-items:flex-end;justify-content:space-between;gap:20px;margin-bottom:22px;flex-wrap:wrap}
.hero h1{font-size:25px;font-weight:800;letter-spacing:-.02em}
.hero h1 .wave{display:inline-block;animation:wave 2.4s ease-in-out infinite;transform-origin:70% 70%}
@keyframes wave{0%,60%,100%{transform:rotate(0)}20%{transform:rotate(16deg)}40%{transform:rotate(-8deg)}}
.hero p{color:var(--txt-2);font-size:13.5px;margin-top:5px}
.hero .quick{display:flex;gap:9px;flex-wrap:wrap}
.qbtn{display:flex;align-items:center;gap:7px;background:var(--card);border:1px solid var(--line-2);
  border-radius:var(--pill);padding:8px 14px;font-size:12.5px;font-weight:600;color:var(--txt);
  cursor:pointer;box-shadow:var(--sh-1);transition:.14s}
.qbtn:hover{border-color:var(--pri);color:var(--pri-d);background:var(--pri-soft)}
.qbtn svg{width:15px;height:15px}

/* KPI cards */
.kpis{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:20px}
.kpi{background:var(--card);border:1px solid var(--line);border-radius:var(--r);padding:18px;
  box-shadow:var(--sh-1);transition:.18s;position:relative;overflow:hidden}
.kpi:hover{box-shadow:var(--sh-2);transform:translateY(-2px)}
.kpi .ic{width:40px;height:40px;border-radius:11px;display:grid;place-items:center;margin-bottom:14px}
.kpi .ic svg{width:20px;height:20px}
.kpi.b1 .ic{background:var(--pri-tint);color:var(--pri)}
.kpi.b2 .ic{background:var(--green-t);color:var(--green)}
.kpi.b3 .ic{background:var(--violet-t);color:var(--violet)}
.kpi.b4 .ic{background:var(--amber-t);color:var(--amber)}
.kpi .num{font-size:30px;font-weight:800;letter-spacing:-.02em;line-height:1}
.kpi .lab{font-size:12.5px;color:var(--txt-2);margin-top:6px;font-weight:500}
.kpi .delta{position:absolute;top:18px;right:18px;font-size:11.5px;font-weight:700;
  display:flex;align-items:center;gap:3px;padding:3px 8px;border-radius:var(--pill)}
.kpi .delta svg{width:12px;height:12px}
.kpi .delta.up{background:var(--green-t);color:var(--green)}
.kpi .delta.down{background:var(--red-t);color:var(--red)}

/* grid sections */
.grid{display:grid;grid-template-columns:1.62fr 1fr;gap:16px;margin-bottom:20px}
.panel{background:var(--card);border:1px solid var(--line);border-radius:var(--r);box-shadow:var(--sh-1)}
.ph{display:flex;align-items:center;justify-content:space-between;padding:16px 18px;border-bottom:1px solid var(--line)}
.ph h3{font-size:14.5px;font-weight:700;display:flex;align-items:center;gap:9px}
.ph h3 .hi{width:26px;height:26px;border-radius:8px;display:grid;place-items:center;background:var(--pri-tint);color:var(--pri)}
.ph h3 .hi svg{width:15px;height:15px}
.ph .more{font-size:12.5px;color:var(--pri-d);font-weight:600;cursor:pointer;display:flex;align-items:center;gap:4px}
.ph .more:hover{text-decoration:underline}
.ph .seg{display:flex;gap:2px;background:var(--card-2);border:1px solid var(--line);border-radius:var(--pill);padding:3px}
.ph .seg span{font-size:11.5px;font-weight:600;color:var(--txt-3);padding:4px 11px;border-radius:var(--pill);cursor:pointer}
.ph .seg span.on{background:#fff;color:var(--txt);box-shadow:var(--sh-1)}
.pb{padding:18px}

/* chart (bars) */
.chart{height:208px;display:flex;align-items:flex-end;gap:14px;padding:8px 4px 0}
.col{flex:1;display:flex;flex-direction:column;align-items:center;gap:9px;height:100%;justify-content:flex-end}
.col .stk{width:100%;max-width:34px;display:flex;flex-direction:column-reverse;border-radius:7px 7px 4px 4px;overflow:hidden}
.col .s1{background:var(--pri)}
.col .s2{background:#9DBDF4}
.col .s3{background:#D5E2FB}
.col .x{font-size:11px;color:var(--txt-3);font-weight:600}
.col:hover .stk{filter:brightness(1.04)}
.legend{display:flex;gap:16px;margin-top:14px;padding-top:14px;border-top:1px solid var(--line);flex-wrap:wrap}
.legend span{display:flex;align-items:center;gap:7px;font-size:12px;color:var(--txt-2);font-weight:500}
.legend i{width:11px;height:11px;border-radius:3px}

/* agent activity / timeline */
.tl{display:flex;flex-direction:column}
.tlitem{display:flex;gap:13px;padding:13px 0;border-bottom:1px solid var(--line)}
.tlitem:last-child{border-bottom:0;padding-bottom:0}
.tlitem .dot{width:32px;height:32px;border-radius:9px;flex:0 0 32px;display:grid;place-items:center}
.tlitem .dot svg{width:16px;height:16px}
.tlitem.live .dot{background:var(--pri-tint);color:var(--pri)}
.tlitem.ok .dot{background:var(--green-t);color:var(--green)}
.tlitem.info .dot{background:var(--violet-t);color:var(--violet)}
.tlitem.warn .dot{background:var(--amber-t);color:var(--amber)}
.tlitem .tx{flex:1;min-width:0}
.tlitem .tx b{font-size:13px;font-weight:600;display:block}
.tlitem .tx p{font-size:12px;color:var(--txt-2);margin-top:2px}
.tlitem .tm{font-size:11px;color:var(--txt-3);white-space:nowrap;font-weight:500}
.tlitem.live .tx b::after{content:"LIVE";font-size:9px;font-weight:800;color:var(--pri);background:var(--pri-tint);
  padding:1px 6px;border-radius:6px;margin-left:8px;letter-spacing:.05em;vertical-align:middle}

/* list rows (matches / candidates) */
.rows{display:flex;flex-direction:column;gap:4px}
.lrow{display:flex;align-items:center;gap:12px;padding:11px 10px;border-radius:var(--r-sm);cursor:pointer;transition:.12s}
.lrow:hover{background:var(--hover)}
.lrow .lg{width:38px;height:38px;border-radius:10px;flex:0 0 38px;display:grid;place-items:center;
  font-weight:700;font-size:14px;color:#fff}
.lrow .info{flex:1;min-width:0}
.lrow .info b{font-size:13px;font-weight:600;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.lrow .info span{font-size:11.5px;color:var(--txt-3)}
.lrow .right{text-align:right}
.lrow .pay{font-size:12.5px;font-weight:700;font-family:var(--mono);color:var(--txt)}
.fit{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:3px 8px;border-radius:var(--pill);
  background:var(--green-t);color:var(--green)}
.fit.mid{background:var(--amber-t);color:var(--amber)}
.fit .ring{width:8px;height:8px;border-radius:50%;background:currentColor}

/* insight box */
.insight{background:var(--pri-soft);border:1px solid #D6E4FF;border-radius:var(--r);padding:15px;margin-top:14px;
  display:flex;gap:12px}
.insight .si{width:30px;height:30px;border-radius:9px;flex:0 0 30px;display:grid;place-items:center;
  background:var(--pri);color:#fff}
.insight .si svg{width:16px;height:16px}
.insight .it b{font-size:12.5px;font-weight:700;color:var(--pri-dd)}
.insight .it p{font-size:12px;color:var(--txt-2);margin-top:3px;line-height:1.5}

/* table */
table{width:100%;border-collapse:collapse}
thead th{text-align:left;font-size:11px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;
  color:var(--txt-3);padding:0 14px 12px;border-bottom:1px solid var(--line)}
tbody td{padding:14px;border-bottom:1px solid var(--line);font-size:13px;vertical-align:middle}
tbody tr:last-child td{border-bottom:0}
tbody tr{transition:.12s}
tbody tr:hover{background:var(--hover)}
.cellco{display:flex;align-items:center;gap:11px}
.cellco .lg{width:34px;height:34px;border-radius:9px;display:grid;place-items:center;color:#fff;font-weight:700;font-size:13px;flex:0 0 34px}
.cellco b{font-size:13px;font-weight:600;display:block}
.cellco span{font-size:11.5px;color:var(--txt-3)}
.tag{font-size:11px;font-weight:700;padding:4px 10px;border-radius:var(--pill);display:inline-flex;align-items:center;gap:5px}
.tag i{width:6px;height:6px;border-radius:50%;background:currentColor}
.tag.rev{background:var(--pri-tint);color:var(--pri-d)}
.tag.int{background:var(--violet-t);color:var(--violet)}
.tag.app{background:var(--amber-t);color:var(--amber)}
.tag.off{background:var(--green-t);color:var(--green)}
.tag.scr{background:var(--teal-t);color:var(--teal)}
.barmini{height:6px;border-radius:4px;background:var(--line);overflow:hidden;width:96px}
.barmini i{display:block;height:100%;border-radius:4px;background:var(--pri)}

/* kanban (company) */
.board{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}
.bcol{background:var(--card-2);border:1px solid var(--line);border-radius:var(--r);padding:12px}
.bcol .bh{display:flex;align-items:center;justify-content:space-between;margin-bottom:11px;padding:0 2px}
.bcol .bh b{font-size:12.5px;font-weight:700}
.bcol .bh .ct{font-size:11px;font-weight:700;color:var(--txt-3);background:var(--card);border:1px solid var(--line);
  border-radius:var(--pill);padding:1px 8px}
.bcol .bh .ct.dotn{color:var(--pri);border-color:#CFE0FF;background:var(--pri-tint)}
.bcard{background:var(--card);border:1px solid var(--line);border-radius:var(--r-sm);padding:12px;margin-bottom:9px;
  box-shadow:var(--sh-1);cursor:grab;transition:.14s}
.bcard:hover{box-shadow:var(--sh-2);transform:translateY(-1px)}
.bcard:last-child{margin-bottom:0}
.bcard .cu{display:flex;align-items:center;gap:9px}
.bcard .cu .av{width:30px;height:30px;border-radius:50%;flex:0 0 30px;display:grid;place-items:center;
  font-weight:700;font-size:11.5px;color:#fff}
.bcard .cu b{font-size:12.5px;font-weight:600}
.bcard .cu span{font-size:11px;color:var(--txt-3)}
.bcard .meta{display:flex;align-items:center;justify-content:space-between;margin-top:10px}
.bcard .role{font-size:11px;color:var(--txt-2);font-weight:500}

.role-block{display:none}
html[data-role="student"] .role-block.student{display:block}
html[data-role="company"] .role-block.company{display:block}
.role-grid{display:none}
html[data-role="student"] .role-grid.student{display:grid}
html[data-role="company"] .role-grid.company{display:grid}

/* ---------- AI assistant ---------- */
.fab{position:fixed;right:26px;bottom:26px;z-index:80;width:58px;height:58px;border-radius:50%;
  background:var(--pri);border:0;cursor:pointer;display:grid;place-items:center;color:#fff;
  box-shadow:0 6px 18px rgba(45,108,223,.5);transition:.2s}
.fab:hover{background:var(--pri-d);transform:scale(1.05)}
.fab svg{width:25px;height:25px}
.fab .nub{position:absolute;top:-4px;right:-4px;background:var(--amber);color:#fff;font-size:10px;font-weight:800;
  width:20px;height:20px;border-radius:50%;display:grid;place-items:center;border:2px solid var(--bg)}
.chat{position:fixed;right:26px;bottom:26px;z-index:90;width:392px;max-width:calc(100vw - 36px);
  height:600px;max-height:calc(100vh - 60px);background:var(--card);border:1px solid var(--line-2);
  border-radius:var(--r-lg);box-shadow:var(--sh-3);display:flex;flex-direction:column;overflow:hidden;
  transform:translateY(16px) scale(.97);opacity:0;pointer-events:none;transform-origin:bottom right;transition:.2s}
.chat.open{transform:none;opacity:1;pointer-events:auto}
.chat .ch{display:flex;align-items:center;gap:12px;padding:16px 18px;border-bottom:1px solid var(--line);
  background:var(--pri-soft)}
.chat .ch .bot{width:38px;height:38px;border-radius:11px;background:var(--pri);display:grid;place-items:center;color:#fff;flex:0 0 38px}
.chat .ch .bot svg{width:20px;height:20px}
.chat .ch .who b{font-size:14px;font-weight:700;display:block}
.chat .ch .who span{font-size:11.5px;color:var(--green);font-weight:600;display:flex;align-items:center;gap:5px}
.chat .ch .who span i{width:7px;height:7px;border-radius:50%;background:var(--green)}
.chat .ch .x{margin-left:auto;width:32px;height:32px;border-radius:8px;border:0;background:transparent;cursor:pointer;
  color:var(--txt-2);display:grid;place-items:center}
.chat .ch .x:hover{background:rgba(0,0,0,.05)}
.thread{flex:1;overflow-y:auto;padding:18px;display:flex;flex-direction:column;gap:14px;background:var(--card-2)}
.msg{display:flex;gap:9px;max-width:86%}
.msg .ma{width:28px;height:28px;border-radius:8px;flex:0 0 28px;display:grid;place-items:center;font-size:11px;font-weight:700}
.msg.bot .ma{background:var(--pri);color:#fff}
.msg.me{align-self:flex-end;flex-direction:row-reverse}
.msg.me .ma{background:var(--txt);color:#fff}
.bub{padding:11px 14px;border-radius:14px;font-size:13px;line-height:1.5;box-shadow:var(--sh-1)}
.msg.bot .bub{background:#fff;border:1px solid var(--line);border-top-left-radius:5px}
.msg.me .bub{background:var(--pri);color:#fff;border-top-right-radius:5px}
.bub b{font-weight:700}
.chips{display:flex;gap:8px;flex-wrap:wrap;padding:0 18px 12px;background:var(--card-2)}
.chip{font-size:12px;font-weight:600;color:var(--pri-d);background:#fff;border:1px solid #CFE0FF;border-radius:var(--pill);
  padding:7px 13px;cursor:pointer;transition:.14s}
.chip:hover{background:var(--pri-tint)}
.cin{display:flex;align-items:center;gap:9px;padding:13px 14px;border-top:1px solid var(--line);background:#fff}
.cin input{flex:1;border:1px solid var(--line-2);border-radius:var(--pill);padding:11px 16px;font-family:inherit;
  font-size:13px;color:var(--txt)}
.cin input:focus{outline:none;border-color:var(--pri);box-shadow:0 0 0 3px var(--pri-tint)}
.cin .send{width:40px;height:40px;border-radius:50%;border:0;background:var(--pri);color:#fff;cursor:pointer;
  display:grid;place-items:center;flex:0 0 40px;transition:.14s}
.cin .send:hover{background:var(--pri-d)}
.cin .send svg{width:18px;height:18px}
.typing{display:flex;gap:4px;align-items:center;padding:11px 14px;background:#fff;border:1px solid var(--line);
  border-radius:14px;border-top-left-radius:5px;width:fit-content}
.typing i{width:7px;height:7px;border-radius:50%;background:var(--txt-3);animation:bln 1.2s infinite}
.typing i:nth-child(2){animation-delay:.15s}.typing i:nth-child(3){animation-delay:.3s}
@keyframes bln{0%,60%,100%{opacity:.3;transform:translateY(0)}30%{opacity:1;transform:translateY(-3px)}}

@media(max-width:1100px){
  .kpis{grid-template-columns:repeat(2,1fr)}
  .grid{grid-template-columns:1fr}
  .board{grid-template-columns:repeat(2,1fr)}
}
@media(max-width:860px){
  .app{grid-template-columns:1fr}
  .side{display:none}
  .kpis{grid-template-columns:1fr}
  .board{grid-template-columns:1fr}
  .search{display:none}
}
</style>
</head>
<body>

<div class="flag"><span class="dot"></span> <b>UI PREVIEW</b> · sample data, not connected to your account</div>

<div class="app">

  <!-- ============ SIDEBAR ============ -->
  <aside class="side">
    <div class="brand">
      <div class="mark" title="hire">
        <svg viewBox="0 0 24 60" fill="none" xmlns="http://www.w3.org/2000/svg" aria-label="hire">
          <path d="M5 3 L19 3 L15 13.5 L9 13.5 Z" fill="#F4C20D"/>
          <path d="M9 13.5 L15 13.5 L18 27.5 L12 56.5 L6 27.5 Z" fill="#2D6CDF"/>
          <path d="M12 16.5 L12 52.5" stroke="#fff" stroke-opacity=".30" stroke-width="1.3" stroke-linecap="round"/>
        </svg>
      </div>
      <div>
        <span class="wordmark">h<svg class="tie" viewBox="0 0 24 60" fill="none" xmlns="http://www.w3.org/2000/svg" aria-label="i"><path d="M5 3 L19 3 L15 13.5 L9 13.5 Z" fill="#F4C20D"/><path d="M9 13.5 L15 13.5 L18 27.5 L12 56.5 L6 27.5 Z" fill="#2D6CDF"/><path d="M12 16.5 L12 52.5" stroke="#fff" stroke-opacity=".30" stroke-width="1.3" stroke-linecap="round"/></svg>re</span>
        <div class="sub">by StudAI One</div>
      </div>
    </div>

    <div class="roleseg">
      <button class="r-st" onclick="setRole('student')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
        Student
      </button>
      <button class="r-co" onclick="setRole('company')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
        Company
      </button>
    </div>

    <nav class="nav">
      <!-- STUDENT NAV -->
      <div class="navlbl role-only student">Workspace</div>
      <div class="navitem on role-only student">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></svg>
        Dashboard
      </div>
      <div class="navitem role-only student">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="3.2"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3M5 5l2 2M17 17l2 2M19 5l-2 2M7 17l-2 2"/></svg>
        Autonomous Agent <span class="badge">3</span>
      </div>
      <div class="navitem role-only student">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
        Job Search
      </div>
      <div class="navitem role-only student">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h6"/></svg>
        Resume Studio
      </div>
      <div class="navitem role-only student">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2M12 19v4"/></svg>
        Interview AI
      </div>
      <div class="navitem role-only student">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 2v20M5 5l14 14M19 5 5 19"/><circle cx="12" cy="12" r="3"/></svg>
        Negotiation Coach <span class="badge soft">new</span>
      </div>
      <div class="navlbl role-only student">Activity</div>
      <div class="navitem role-only student">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 11l3 3 8-8"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
        Applications <span class="badge">12</span>
      </div>
      <div class="navitem role-only student">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="8" r="4"/><path d="M4 21v-1a6 6 0 0 1 6-6h4a6 6 0 0 1 6 6v1"/></svg>
        My Profile
      </div>

      <!-- COMPANY NAV -->
      <div class="navlbl role-only company">S.C.O.U.T.</div>
      <div class="navitem on role-only company">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></svg>
        Dashboard
      </div>
      <div class="navitem role-only company">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18M8 4V2M16 4V2"/></svg>
        Job Postings <span class="badge">7</span>
      </div>
      <div class="navitem role-only company">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="9" cy="8" r="4"/><path d="M3 21v-1a6 6 0 0 1 6-6M16 11l2 2 4-4"/></svg>
        Candidates <span class="badge">148</span>
      </div>
      <div class="navitem role-only company">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z"/></svg>
        Pipeline
      </div>
      <div class="navitem role-only company">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
        Interviews <span class="badge soft">9</span>
      </div>
      <div class="navlbl role-only company">Insights</div>
      <div class="navitem role-only company">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 3v18h18"/><path d="M7 15l3-4 3 2 5-6"/></svg>
        Analytics
      </div>
      <div class="navitem role-only company">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
        Talent Search
      </div>

      <div class="promo">
        <h5>
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m12 2 2.4 7.4H22l-6 4.4 2.3 7.2-6.3-4.6L5.7 21 8 14 2 9.4h7.6z"/></svg>
          Upgrade to Pro
        </h5>
        <p class="role-only student" style="display:block">Unlock unlimited auto-applies & priority agent runs.</p>
        <p class="role-only company" style="display:none">Unlock unlimited job slots & AI candidate ranking.</p>
        <button>Upgrade — ₹499/mo</button>
      </div>
    </nav>

    <div class="usercard">
      <div class="av" id="uav">AR</div>
      <div>
        <div class="nm" id="unm">Aarav Reddy</div>
        <div class="pl" id="upl">Free plan</div>
      </div>
      <svg class="chev" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
    </div>
  </aside>

  <!-- ============ MAIN ============ -->
  <div class="main">

    <header class="top">
      <div class="crumb">
        <span id="cr-ctx">Workspace</span>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
        <b>Dashboard</b>
      </div>
      <div class="search">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
        <input id="srch" placeholder="Search jobs, candidates, anything…">
        <kbd>⌘K</kbd>
      </div>
      <div class="ttools">
        <button class="newbtn" onclick="openChat()">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8V4M8 12H4M12 16v4M16 12h4"/><circle cx="12" cy="12" r="3"/></svg>
          <span id="newlbl">New search</span>
        </button>
        <button class="iconbtn" title="Notifications"><span class="ping"></span>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/></svg>
        </button>
        <button class="iconbtn" title="Help">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.1 9a3 3 0 0 1 5.8 1c0 2-3 2.5-3 3.5"/><circle cx="12" cy="17" r=".6" fill="currentColor"/></svg>
        </button>
      </div>
    </header>

    <main class="content">

      <!-- hero -->
      <div class="hero">
        <div>
          <h1><span id="greet">Good morning, Aarav</span> <span class="wave">👋</span></h1>
          <p id="subgreet">Your agent applied to 4 roles overnight. Here's where things stand.</p>
        </div>
        <div class="quick" id="quick">
          <!-- filled by JS -->
        </div>
      </div>

      <!-- KPIs -->
      <div class="kpis" id="kpis"><!-- filled by JS --></div>

      <!-- grid: chart + activity -->
      <div class="grid">
        <div class="panel">
          <div class="ph">
            <h3><span class="hi"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="M7 14l3-3 3 2 4-5"/></svg></span> <span id="chartTtl">Application activity</span></h3>
            <div class="seg"><span>Week</span><span class="on">Month</span><span>Year</span></div>
          </div>
          <div class="pb">
            <div class="chart" id="chart"><!-- bars by JS --></div>
            <div class="legend" id="legend"><!-- by JS --></div>
          </div>
        </div>

        <div class="panel">
          <div class="ph">
            <h3><span class="hi"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3.2"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3"/></svg></span> Agent activity</h3>
            <span class="more">View all</span>
          </div>
          <div class="pb">
            <div class="tl" id="timeline"><!-- by JS --></div>
          </div>
        </div>
      </div>

      <!-- grid: list + insight -->
      <div class="grid">
        <div class="panel">
          <div class="ph">
            <h3><span class="hi"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7h18M3 12h18M3 17h12"/></svg></span> <span id="listTtl">Top matches for you</span></h3>
            <span class="more" id="listMore">Browse all <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg></span>
          </div>
          <div class="pb">
            <div class="rows" id="matchrows"><!-- by JS --></div>
          </div>
        </div>

        <div class="panel">
          <div class="ph">
            <h3><span class="hi"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18h6M10 22h4M12 2a7 7 0 0 0-4 12.7c.6.5 1 1.3 1 2.3h6c0-1 .4-1.8 1-2.3A7 7 0 0 0 12 2z"/></svg></span> <span id="coachTtl">Coach insight</span></h3>
          </div>
          <div class="pb">
            <div id="coachBody"><!-- by JS --></div>
          </div>
        </div>
      </div>

      <!-- STUDENT: applications table -->
      <div class="role-block student">
        <div class="panel">
          <div class="ph">
            <h3><span class="hi"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3 8-8"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></span> Your applications</h3>
            <span class="more">Manage <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg></span>
          </div>
          <div class="pb" style="padding-top:6px">
            <table>
              <thead><tr><th>Company / Role</th><th>Applied via</th><th>Match</th><th>Stage</th><th>Updated</th></tr></thead>
              <tbody>
                <tr>
                  <td><div class="cellco"><div class="lg" style="background:#2D6CDF">R</div><div><b>Razorpay</b><span>Backend Engineer · Bengaluru</span></div></div></td>
                  <td>Agent · auto</td>
                  <td><div style="display:flex;align-items:center;gap:9px"><div class="barmini"><i style="width:94%"></i></div><b style="font-family:var(--mono);font-size:12px">94</b></div></td>
                  <td><span class="tag off"><i></i>Offer</span></td>
                  <td style="color:var(--txt-3)">2h ago</td>
                </tr>
                <tr>
                  <td><div class="cellco"><div class="lg" style="background:#E37400">S</div><div><b>Swiggy</b><span>SDE-2 · Hyderabad</span></div></div></td>
                  <td>Agent · auto</td>
                  <td><div style="display:flex;align-items:center;gap:9px"><div class="barmini"><i style="width:88%"></i></div><b style="font-family:var(--mono);font-size:12px">88</b></div></td>
                  <td><span class="tag int"><i></i>Interview</span></td>
                  <td style="color:var(--txt-3)">5h ago</td>
                </tr>
                <tr>
                  <td><div class="cellco"><div class="lg" style="background:#7C4DFF">F</div><div><b>Flipkart</b><span>Full-stack · Remote</span></div></div></td>
                  <td>You · manual</td>
                  <td><div style="display:flex;align-items:center;gap:9px"><div class="barmini"><i style="width:81%"></i></div><b style="font-family:var(--mono);font-size:12px">81</b></div></td>
                  <td><span class="tag rev"><i></i>In review</span></td>
                  <td style="color:var(--txt-3)">1d ago</td>
                </tr>
                <tr>
                  <td><div class="cellco"><div class="lg" style="background:#00897B">Z</div><div><b>Zoho</b><span>Product Engineer · Chennai</span></div></div></td>
                  <td>Agent · auto</td>
                  <td><div style="display:flex;align-items:center;gap:9px"><div class="barmini"><i style="width:76%"></i></div><b style="font-family:var(--mono);font-size:12px">76</b></div></td>
                  <td><span class="tag app"><i></i>Applied</span></td>
                  <td style="color:var(--txt-3)">1d ago</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- COMPANY: kanban + recent -->
      <div class="role-block company">
        <div class="panel" style="margin-bottom:20px">
          <div class="ph">
            <h3><span class="hi"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z"/></svg></span> Hiring pipeline — Backend Engineer</h3>
            <div class="seg"><span class="on">Board</span><span>List</span></div>
          </div>
          <div class="pb">
            <div class="board">
              <div class="bcol">
                <div class="bh"><b>New</b><span class="ct dotn">24</span></div>
                <div class="bcard"><div class="cu"><div class="av" style="background:#2D6CDF">PK</div><div><b>Priya Krishnan</b><span>6 yrs · Bengaluru</span></div></div><div class="meta"><span class="role">Backend</span><span class="fit"><span class="ring"></span>92 fit</span></div></div>
                <div class="bcard"><div class="cu"><div class="av" style="background:#7C4DFF">DS</div><div><b>Dev Sharma</b><span>4 yrs · Remote</span></div></div><div class="meta"><span class="role">Backend</span><span class="fit mid"><span class="ring"></span>78 fit</span></div></div>
              </div>
              <div class="bcol">
                <div class="bh"><b>Screening</b><span class="ct">18</span></div>
                <div class="bcard"><div class="cu"><div class="av" style="background:#E37400">AN</div><div><b>Ananya Nair</b><span>5 yrs · Pune</span></div></div><div class="meta"><span class="role">Backend</span><span class="fit"><span class="ring"></span>89 fit</span></div></div>
                <div class="bcard"><div class="cu"><div class="av" style="background:#00897B">KI</div><div><b>Karan Iyer</b><span>7 yrs · Mumbai</span></div></div><div class="meta"><span class="role">Backend</span><span class="fit"><span class="ring"></span>85 fit</span></div></div>
              </div>
              <div class="bcol">
                <div class="bh"><b>Interview</b><span class="ct">9</span></div>
                <div class="bcard"><div class="cu"><div class="av" style="background:#D93025">SM</div><div><b>Sneha Menon</b><span>6 yrs · Bengaluru</span></div></div><div class="meta"><span class="role">Backend</span><span class="fit"><span class="ring"></span>91 fit</span></div></div>
              </div>
              <div class="bcol">
                <div class="bh"><b>Offer</b><span class="ct">3</span></div>
                <div class="bcard"><div class="cu"><div class="av" style="background:#1E8E3E">RV</div><div><b>Rahul Verma</b><span>8 yrs · Hyderabad</span></div></div><div class="meta"><span class="role">Backend</span><span class="fit"><span class="ring"></span>95 fit</span></div></div>
              </div>
            </div>
          </div>
        </div>

        <div class="panel">
          <div class="ph">
            <h3><span class="hi"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="8" r="4"/><path d="M3 21v-1a6 6 0 0 1 6-6M16 11l2 2 4-4"/></svg></span> Recent applicants</h3>
            <span class="more">View all <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg></span>
          </div>
          <div class="pb" style="padding-top:6px">
            <table>
              <thead><tr><th>Candidate</th><th>Role</th><th>AI fit</th><th>Stage</th><th>Applied</th></tr></thead>
              <tbody>
                <tr>
                  <td><div class="cellco"><div class="lg" style="background:#2D6CDF">PK</div><div><b>Priya Krishnan</b><span>6 yrs · Bengaluru</span></div></div></td>
                  <td>Backend Engineer</td>
                  <td><div style="display:flex;align-items:center;gap:9px"><div class="barmini"><i style="width:92%"></i></div><b style="font-family:var(--mono);font-size:12px">92</b></div></td>
                  <td><span class="tag scr"><i></i>Screening</span></td>
                  <td style="color:var(--txt-3)">1h ago</td>
                </tr>
                <tr>
                  <td><div class="cellco"><div class="lg" style="background:#D93025">SM</div><div><b>Sneha Menon</b><span>6 yrs · Bengaluru</span></div></div></td>
                  <td>Backend Engineer</td>
                  <td><div style="display:flex;align-items:center;gap:9px"><div class="barmini"><i style="width:91%"></i></div><b style="font-family:var(--mono);font-size:12px">91</b></div></td>
                  <td><span class="tag int"><i></i>Interview</span></td>
                  <td style="color:var(--txt-3)">3h ago</td>
                </tr>
                <tr>
                  <td><div class="cellco"><div class="lg" style="background:#E37400">AN</div><div><b>Ananya Nair</b><span>5 yrs · Pune</span></div></div></td>
                  <td>Backend Engineer</td>
                  <td><div style="display:flex;align-items:center;gap:9px"><div class="barmini"><i style="width:89%"></i></div><b style="font-family:var(--mono);font-size:12px">89</b></div></td>
                  <td><span class="tag scr"><i></i>Screening</span></td>
                  <td style="color:var(--txt-3)">6h ago</td>
                </tr>
                <tr>
                  <td><div class="cellco"><div class="lg" style="background:#1E8E3E">RV</div><div><b>Rahul Verma</b><span>8 yrs · Hyderabad</span></div></div></td>
                  <td>Senior Backend</td>
                  <td><div style="display:flex;align-items:center;gap:9px"><div class="barmini"><i style="width:95%"></i></div><b style="font-family:var(--mono);font-size:12px">95</b></div></td>
                  <td><span class="tag off"><i></i>Offer</span></td>
                  <td style="color:var(--txt-3)">1d ago</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </main>
  </div>
</div>

<!-- ============ AI ASSISTANT ============ -->
<button class="fab" id="fab" onclick="openChat()" title="Ask your AI assistant">
  <span class="nub">1</span>
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a9 9 0 0 0-9 9c0 1.6.4 3 1.1 4.3L3 21l5.9-1.1A9 9 0 1 0 12 2z"/><circle cx="9" cy="11" r="1" fill="currentColor"/><circle cx="12" cy="11" r="1" fill="currentColor"/><circle cx="15" cy="11" r="1" fill="currentColor"/></svg>
</button>

<div class="chat" id="chat">
  <div class="ch">
    <div class="bot"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a9 9 0 0 0-9 9c0 1.6.4 3 1.1 4.3L3 21l5.9-1.1A9 9 0 1 0 12 2z"/></svg></div>
    <div class="who"><b id="botName">Career Copilot</b><span><i></i>Online · powered by StudAI</span></div>
    <button class="x" onclick="closeChat()"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg></button>
  </div>
  <div class="thread" id="thread"></div>
  <div class="chips" id="chips"></div>
  <div class="cin">
    <input id="cinput" placeholder="Ask anything…" onkeydown="if(event.key==='Enter')sendMsg()">
    <button class="send" onclick="sendMsg()"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m22 2-7 20-4-9-9-4z"/><path d="M22 2 11 13"/></svg></button>
  </div>
</div>

<script>
const DATA = {
  student:{
    name:"Aarav Reddy", av:"AR", plan:"Free plan", ctx:"Workspace",
    greet:"Good morning, Aarav", sub:"Your agent applied to 4 roles overnight. Here's where things stand.",
    newBtn:"New search", botName:"Career Copilot",
    quick:[
      {i:'<circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/>',t:"Find jobs"},
      {i:'<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/>',t:"Tailor resume"},
      {i:'<path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/>',t:"Mock interview"},
    ],
    kpis:[
      {c:"b1",i:'<path d="M9 11l3 3 8-8"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>',n:"12",l:"Active applications",d:"+4",up:true},
      {c:"b2",i:'<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.9"/>',n:"7",l:"Interviews scheduled",d:"+2",up:true},
      {c:"b3",i:'<path d="M12 2 2 7l10 5 10-5z"/><path d="M2 17l10 5 10-5M2 12l10 5 10-5"/>',n:"94",l:"Top match score",d:"+6",up:true},
      {c:"b4",i:'<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',n:"38h",l:"Saved by your agent",d:"this week",up:true},
    ],
    chartTtl:"Application activity",
    chart:[ {x:"Mon",s:[5,2,1]},{x:"Tue",s:[7,3,2]},{x:"Wed",s:[4,4,1]},{x:"Thu",s:[9,3,3]},{x:"Fri",s:[6,5,2]},{x:"Sat",s:[3,2,1]},{x:"Sun",s:[8,4,2]} ],
    legend:[{c:"var(--pri)",t:"Applied"},{c:"#9DBDF4",t:"Viewed"},{c:"#D5E2FB",t:"Responses"}],
    timeline:[
      {k:"live",i:'<circle cx="12" cy="12" r="3.2"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3"/>',b:"Applying to Razorpay",p:"Tailoring your resume to the JD…",t:"now"},
      {k:"ok",i:'<path d="M20 6 9 17l-5-5"/>',b:"Applied to Swiggy SDE-2",p:"Cover letter generated & submitted",t:"2h"},
      {k:"info",i:'<path d="M22 2 11 13M22 2l-7 20-4-9-9-4z"/>',b:"3 new matches found",p:"Backend roles above 85% fit",t:"5h"},
      {k:"warn",i:'<path d="M12 9v4M12 17h.01"/><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/>',b:"Resume needs a skill",p:"Add 'Kubernetes' to boost 4 matches",t:"1d"},
    ],
    listTtl:"Top matches for you", listMore:"Browse all",
    matches:[
      {lg:"R",bg:"#2D6CDF",b:"Razorpay — Backend Engineer",s:"Bengaluru · ₹28–36 LPA",pay:"94 fit",mid:false},
      {lg:"S",bg:"#E37400",b:"Swiggy — SDE-2",s:"Hyderabad · ₹24–32 LPA",pay:"88 fit",mid:false},
      {lg:"F",bg:"#7C4DFF",b:"Flipkart — Full-stack",s:"Remote · ₹22–30 LPA",pay:"81 fit",mid:true},
      {lg:"Z",bg:"#00897B",b:"Zoho — Product Engineer",s:"Chennai · ₹18–26 LPA",pay:"76 fit",mid:true},
    ],
    coachTtl:"Coach insight",
    coach:`<div class="insight"><div class="si"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18h6M10 22h4M12 2a7 7 0 0 0-4 12.7c.6.5 1 1.3 1 2.3h6c0-1 .4-1.8 1-2.3A7 7 0 0 0 12 2z"/></svg></div><div class="it"><b>You're interview-ready for Razorpay</b><p>Your system-design answers improved 22% this week. Practice 2 more behavioral rounds to be fully prepared.</p></div></div>
      <div style="margin-top:14px;display:flex;flex-direction:column;gap:11px">
        <div style="display:flex;justify-content:space-between;font-size:12.5px"><span style="color:var(--txt-2);font-weight:500">Profile strength</span><b>86%</b></div>
        <div class="barmini" style="width:100%"><i style="width:86%"></i></div>
        <div style="display:flex;justify-content:space-between;font-size:12.5px;margin-top:4px"><span style="color:var(--txt-2);font-weight:500">Interview readiness</span><b>78%</b></div>
        <div class="barmini" style="width:100%"><i style="width:78%;background:var(--green)"></i></div>
      </div>`,
    chips:["What should I improve?","Find remote jobs","Prep me for Razorpay"],
    reply(q){
      q=q.toLowerCase();
      if(q.includes("improve"))return "Add <b>Kubernetes</b> and <b>system design</b> proof to your profile — it unlocks 4 higher-paying matches and lifts your average fit to ~90%.";
      if(q.includes("remote"))return "I found <b>6 remote backend roles</b> above 80% fit. Flipkart (₹22–30 LPA) and 2 others are open to negotiation. Want me to auto-apply?";
      if(q.includes("razorpay")||q.includes("prep"))return "For Razorpay I'll run a <b>system-design mock</b> + 2 behavioral rounds. You're at 78% readiness — one focused session should get you to ~90%.";
      return "On it. Your agent is tracking 12 applications and 7 interviews. Want me to prioritise the Razorpay offer or find more matches?";
    }
  },
  company:{
    name:"Meera Kapoor", av:"MK", plan:"S.C.O.U.T. Pro", ctx:"S.C.O.U.T.",
    greet:"Good morning, Meera", sub:"S.C.O.U.T. screened 32 candidates overnight and surfaced 5 strong fits.",
    newBtn:"Post a job", botName:"S.C.O.U.T. Copilot",
    quick:[
      {i:'<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18"/>',t:"Post a job"},
      {i:'<circle cx="9" cy="8" r="4"/><path d="M3 21v-1a6 6 0 0 1 6-6"/>',t:"Search talent"},
      {i:'<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M16 2v4M8 2v4"/>',t:"Schedule interview"},
    ],
    kpis:[
      {c:"b1",i:'<circle cx="9" cy="8" r="4"/><path d="M3 21v-1a6 6 0 0 1 6-6M16 11l2 2 4-4"/>',n:"148",l:"Active candidates",d:"+32",up:true},
      {c:"b2",i:'<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18"/>',n:"7",l:"Open positions",d:"+1",up:true},
      {c:"b3",i:'<path d="M12 2 2 7l10 5 10-5z"/><path d="M2 17l10 5 10-5"/>',n:"91",l:"Avg AI-fit (top 5)",d:"+4",up:true},
      {c:"b4",i:'<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',n:"11d",l:"Avg time-to-hire",d:"−3d",up:true},
    ],
    chartTtl:"Candidate funnel",
    chart:[ {x:"Mon",s:[18,9,3]},{x:"Tue",s:[24,12,4]},{x:"Wed",s:[15,8,2]},{x:"Thu",s:[30,15,6]},{x:"Fri",s:[22,11,5]},{x:"Sat",s:[9,4,1]},{x:"Sun",s:[26,13,5]} ],
    legend:[{c:"var(--pri)",t:"Applied"},{c:"#9DBDF4",t:"Screened"},{c:"#D5E2FB",t:"Interviewed"}],
    timeline:[
      {k:"live",i:'<circle cx="12" cy="12" r="3.2"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3"/>',b:"Ranking 24 new applicants",p:"Scoring against Backend Engineer JD…",t:"now"},
      {k:"ok",i:'<path d="M20 6 9 17l-5-5"/>',b:"5 strong fits surfaced",p:"All above 88% AI-fit score",t:"1h"},
      {k:"info",i:'<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M16 2v4M8 2v4"/>',b:"3 interviews auto-scheduled",p:"Calendar invites sent to panel",t:"4h"},
      {k:"warn",i:'<path d="M12 9v4M12 17h.01"/><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/>',b:"2 offers awaiting response",p:"Rahul Verma — follow up today",t:"1d"},
    ],
    listTtl:"Top candidates", listMore:"View pipeline",
    matches:[
      {lg:"RV",bg:"#1E8E3E",b:"Rahul Verma — Senior Backend",s:"8 yrs · Hyderabad · offer stage",pay:"95 fit",mid:false},
      {lg:"PK",bg:"#2D6CDF",b:"Priya Krishnan — Backend",s:"6 yrs · Bengaluru · screening",pay:"92 fit",mid:false},
      {lg:"SM",bg:"#D93025",b:"Sneha Menon — Backend",s:"6 yrs · Bengaluru · interview",pay:"91 fit",mid:false},
      {lg:"AN",bg:"#E37400",b:"Ananya Nair — Backend",s:"5 yrs · Pune · screening",pay:"89 fit",mid:true},
    ],
    coachTtl:"S.C.O.U.T. insight",
    coach:`<div class="insight"><div class="si"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18h6M10 22h4M12 2a7 7 0 0 0-4 12.7c.6.5 1 1.3 1 2.3h6c0-1 .4-1.8 1-2.3A7 7 0 0 0 12 2z"/></svg></div><div class="it"><b>Your Backend pipeline is healthy</b><p>5 candidates above 88% fit. Rahul Verma is your strongest — move on the offer within 48h to avoid drop-off.</p></div></div>
      <div style="margin-top:14px;display:flex;flex-direction:column;gap:11px">
        <div style="display:flex;justify-content:space-between;font-size:12.5px"><span style="color:var(--txt-2);font-weight:500">Pipeline coverage</span><b>Strong</b></div>
        <div class="barmini" style="width:100%"><i style="width:84%"></i></div>
        <div style="display:flex;justify-content:space-between;font-size:12.5px;margin-top:4px"><span style="color:var(--txt-2);font-weight:500">Offer acceptance (90d)</span><b>72%</b></div>
        <div class="barmini" style="width:100%"><i style="width:72%;background:var(--green)"></i></div>
      </div>`,
    chips:["Who should I hire first?","Summarise top 5","Speed up screening"],
    reply(q){
      q=q.toLowerCase();
      if(q.includes("hire")||q.includes("first"))return "Move on <b>Rahul Verma</b> (95% fit, offer stage) first — strongest match and at risk of a competing offer. I can draft the offer letter now.";
      if(q.includes("summar")||q.includes("top"))return "Top 5 average <b>91% fit</b>: Verma (95), Krishnan (92), Menon (91), Nair (89), Iyer (85). All clear the bar for Backend Engineer.";
      if(q.includes("screen")||q.includes("speed"))return "I can auto-screen the 24 new applicants and shortlist anyone above 85% fit — that usually saves ~6 hours. Shall I run it?";
      return "S.C.O.U.T. is tracking 148 candidates across 7 roles. 5 strong fits are ready for your review — want a side-by-side comparison?";
    }
  }
};

let ROLE = "student";
function d(){return DATA[ROLE]}

function render(){
  const x = d();
  document.documentElement.setAttribute("data-role", ROLE);
  document.getElementById("uav").textContent = x.av;
  document.getElementById("unm").textContent = x.name;
  document.getElementById("upl").textContent = x.plan;
  document.getElementById("cr-ctx").textContent = x.ctx;
  document.getElementById("greet").textContent = x.greet;
  document.getElementById("subgreet").textContent = x.sub;
  document.getElementById("newlbl").textContent = x.newBtn;
  document.getElementById("botName").textContent = x.botName;
  document.getElementById("chartTtl").textContent = x.chartTtl;
  document.getElementById("listTtl").textContent = x.listTtl;
  document.getElementById("listMore").innerHTML = x.listMore + ' <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>';
  document.getElementById("coachTtl").textContent = x.coachTtl;

  // quick
  document.getElementById("quick").innerHTML = x.quick.map(q=>
    `<button class="qbtn" onclick="openChat()"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">${q.i}</svg>${q.t}</button>`).join("");

  // kpis
  document.getElementById("kpis").innerHTML = x.kpis.map(k=>
    `<div class="kpi ${k.c}"><div class="delta ${k.up?'up':'down'}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">${k.up?'<path d="M7 17 17 7M9 7h8v8"/>':'<path d="M7 7l10 10M17 9v8H9"/>'}</svg>${k.d}</div>
      <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">${k.i}</svg></div>
      <div class="num">${k.n}</div><div class="lab">${k.l}</div></div>`).join("");

  // chart
  const maxv = Math.max(...x.chart.map(c=>c.s.reduce((a,b)=>a+b,0)));
  document.getElementById("chart").innerHTML = x.chart.map(c=>{
    const tot = c.s.reduce((a,b)=>a+b,0);
    const h = Math.round((tot/maxv)*180)+8;
    const seg = c.s.map((v,i)=>`<div class="s${i+1}" style="height:${Math.round((v/tot)*h)}px"></div>`).join("");
    return `<div class="col"><div class="stk" style="height:${h}px">${seg}</div><div class="x">${c.x}</div></div>`;
  }).join("");
  document.getElementById("legend").innerHTML = x.legend.map(l=>`<span><i style="background:${l.c}"></i>${l.t}</span>`).join("");

  // timeline
  document.getElementById("timeline").innerHTML = x.timeline.map(t=>
    `<div class="tlitem ${t.k}"><div class="dot"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">${t.i}</svg></div>
      <div class="tx"><b>${t.b}</b><p>${t.p}</p></div><div class="tm">${t.t}</div></div>`).join("");

  // matches
  document.getElementById("matchrows").innerHTML = x.matches.map(m=>
    `<div class="lrow"><div class="lg" style="background:${m.bg}">${m.lg}</div>
      <div class="info"><b>${m.b}</b><span>${m.s}</span></div>
      <div class="right"><span class="fit ${m.mid?'mid':''}"><span class="ring"></span>${m.pay}</span></div></div>`).join("");

  // coach
  document.getElementById("coachBody").innerHTML = x.coach;

  // chat
  document.getElementById("chips").innerHTML = x.chips.map(c=>`<span class="chip" onclick="quickAsk('${c.replace(/'/g,"\\'")}')">${c}</span>`).join("");
  resetThread();
}

function setRole(r){ if(r===ROLE)return; ROLE=r; render(); }

function resetThread(){
  const x = d();
  document.getElementById("thread").innerHTML =
    `<div class="msg bot"><div class="ma">${x.av==='AR'?'S':'S'}</div><div class="bub">Hi ${x.name.split(' ')[0]}! I'm your <b>${x.botName}</b>. ${ROLE==='student'?"Ask me to find jobs, tailor your resume, or prep for interviews.":"Ask me to rank candidates, summarise your pipeline, or schedule interviews."}</div></div>`;
}
function bubble(who,html){
  const t = document.getElementById("thread");
  const av = who==='me' ? d().av : 'S';
  t.insertAdjacentHTML("beforeend",`<div class="msg ${who}"><div class="ma">${av}</div><div class="bub">${html}</div></div>`);
  t.scrollTop = t.scrollHeight;
}
function quickAsk(q){ document.getElementById("cinput").value=q; sendMsg(); }
function sendMsg(){
  const inp = document.getElementById("cinput");
  const v = inp.value.trim(); if(!v)return;
  bubble("me", v.replace(/</g,"&lt;")); inp.value="";
  const t = document.getElementById("thread");
  t.insertAdjacentHTML("beforeend",`<div class="msg bot" id="typ"><div class="ma">S</div><div class="typing"><i></i><i></i><i></i></div></div>`);
  t.scrollTop = t.scrollHeight;
  setTimeout(()=>{ const e=document.getElementById("typ"); if(e)e.remove(); bubble("bot", d().reply(v)); }, 1050);
}
function openChat(){ document.getElementById("chat").classList.add("open"); document.getElementById("fab").style.display="none"; setTimeout(()=>document.getElementById("cinput").focus(),200); }
function closeChat(){ document.getElementById("chat").classList.remove("open"); document.getElementById("fab").style.display="grid"; }

// keyboard
document.addEventListener("keydown",e=>{
  if((e.metaKey||e.ctrlKey)&&e.key.toLowerCase()==="k"){e.preventDefault();document.getElementById("srch").focus();}
  if(e.shiftKey&&e.key.toLowerCase()==="r"){e.preventDefault();setRole(ROLE==="student"?"company":"student");}
  if(e.key==="Escape")closeChat();
});

render();
</script>
</body>
</html>
