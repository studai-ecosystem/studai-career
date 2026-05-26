{{-- Cookie Consent - Pure Vanilla JS --}}
<div id="cc-banner" style="display:none;position:fixed;bottom:0;left:0;right:0;z-index:99999;padding:12px 16px 20px">
    <div style="max-width:960px;margin:0 auto;background:#fff;border:1px solid #e5e7eb;border-radius:20px;box-shadow:0 20px 60px rgba(0,0,0,.18);padding:20px 24px">
        <div style="display:flex;flex-wrap:wrap;align-items:center;gap:16px">
            <div style="flex:1;min-width:220px;display:flex;align-items:flex-start;gap:12px">
                <div style="width:40px;height:40px;border-radius:12px;background:#eef2ff;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg style="width:20px;height:20px;color:#6366f1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                </div>
                <div>
                    <h3 style="font-size:15px;font-weight:700;color:#111827;margin:0 0 4px">We Value Your Privacy</h3>
                    <p style="font-size:13px;color:#6b7280;margin:0;line-height:1.5">We use cookies to enhance your experience and analyze site traffic. <a href="/privacy-policy" style="color:#6366f1;text-decoration:underline">Privacy Policy</a>.</p>
                </div>
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:8px;flex-shrink:0">
                <button onclick="ccAcceptAll()" style="padding:10px 20px;background:linear-gradient(135deg,#6366f1,#7c3aed);color:#fff;font-size:13px;font-weight:600;border:none;border-radius:12px;cursor:pointer">Accept All</button>
                <button onclick="ccEssentialOnly()" style="padding:10px 20px;background:#f3f4f6;color:#374151;font-size:13px;font-weight:600;border:none;border-radius:12px;cursor:pointer">Essential Only</button>
                <button onclick="ccShowModal()" style="padding:10px 20px;background:transparent;color:#6b7280;font-size:13px;font-weight:600;border:1.5px solid #d1d5db;border-radius:12px;cursor:pointer">Customize</button>
            </div>
        </div>
    </div>
</div>

<div id="cc-modal" style="display:none;position:fixed;inset:0;z-index:99999;padding:16px;background:rgba(15,23,42,.6)">
    <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;padding:16px">
        <div style="background:#fff;border-radius:20px;box-shadow:0 32px 80px rgba(0,0,0,.3);width:100%;max-width:560px;max-height:90vh;display:flex;flex-direction:column;overflow:hidden">
            <div style="padding:22px 24px 16px;border-bottom:1px solid #f3f4f6;flex-shrink:0">
                <h3 style="font-size:18px;font-weight:700;color:#111827;margin:0 0 4px">Cookie Preferences</h3>
                <p style="font-size:13px;color:#6b7280;margin:0">Manage your cookie settings. Essential cookies cannot be disabled.</p>
            </div>
            <div style="padding:16px 24px;overflow-y:auto;flex:1;display:flex;flex-direction:column;gap:12px">
                <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:14px;padding:16px">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px">
                        <div>
                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px">
                                <span style="font-size:14px;font-weight:600;color:#111827">Essential Cookies</span>
                                <span style="font-size:11px;background:#16a34a;color:#fff;padding:2px 8px;border-radius:99px;font-weight:600">Always Active</span>
                            </div>
                            <p style="font-size:12px;color:#4b7c59;margin:0">Required for authentication, security, and navigation.</p>
                        </div>
                        <div style="width:40px;height:22px;background:#16a34a;border-radius:99px;position:relative;flex-shrink:0"><div style="position:absolute;right:2px;top:2px;width:18px;height:18px;background:#fff;border-radius:50%"></div></div>
                    </div>
                </div>
                <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:16px">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px">
                        <div style="flex:1">
                            <div style="font-size:14px;font-weight:600;color:#111827;margin-bottom:4px">Performance Cookies</div>
                            <p style="font-size:12px;color:#64748b;margin:0">Analytics and error tracking to improve our service.</p>
                            <p style="font-size:11px;color:#94a3b8;margin:3px 0 0">Google Analytics, error monitoring</p>
                        </div>
                        <button id="cc-perf-btn" onclick="ccToggleBtn(this,'#6366f1')" style="width:40px;height:22px;border-radius:99px;border:none;cursor:pointer;background:#6366f1;position:relative;flex-shrink:0;transition:background .2s">
                            <span id="cc-perf-dot" style="position:absolute;right:2px;top:2px;width:18px;height:18px;background:#fff;border-radius:50%;transition:transform .2s"></span>
                        </button>
                    </div>
                </div>
                <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:16px">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px">
                        <div style="flex:1">
                            <div style="font-size:14px;font-weight:600;color:#111827;margin-bottom:4px">Functional Cookies</div>
                            <p style="font-size:12px;color:#64748b;margin:0">Remember preferences and personalize your experience.</p>
                            <p style="font-size:11px;color:#94a3b8;margin:3px 0 0">Language, theme settings, saved filters</p>
                        </div>
                        <button id="cc-func-btn" onclick="ccToggleBtn(this,'#a855f7')" style="width:40px;height:22px;border-radius:99px;border:none;cursor:pointer;background:#a855f7;position:relative;flex-shrink:0;transition:background .2s">
                            <span id="cc-func-dot" style="position:absolute;right:2px;top:2px;width:18px;height:18px;background:#fff;border-radius:50%;transition:transform .2s"></span>
                        </button>
                    </div>
                </div>
                <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:16px">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px">
                        <div style="flex:1">
                            <div style="font-size:14px;font-weight:600;color:#111827;margin-bottom:4px">Marketing Cookies</div>
                            <p style="font-size:12px;color:#64748b;margin:0">Relevant ads and campaign tracking via third parties.</p>
                            <p style="font-size:11px;color:#94a3b8;margin:3px 0 0">LinkedIn Insight, Google Ads, Facebook Pixel</p>
                        </div>
                        <button id="cc-mkt-btn" onclick="ccToggleBtn(this,'#ec4899')" style="width:40px;height:22px;border-radius:99px;border:none;cursor:pointer;background:#d1d5db;position:relative;flex-shrink:0;transition:background .2s">
                            <span id="cc-mkt-dot" style="position:absolute;left:2px;top:2px;width:18px;height:18px;background:#fff;border-radius:50%;transition:transform .2s"></span>
                        </button>
                    </div>
                </div>
            </div>
            <div style="padding:14px 24px;border-top:1px solid #f3f4f6;flex-shrink:0;display:flex;align-items:center;justify-content:space-between;gap:12px;background:#f9fafb;border-radius:0 0 20px 20px;flex-wrap:wrap">
                <button onclick="ccHideModal()" style="background:none;border:none;font-size:13px;color:#6b7280;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px;padding:4px">
                    <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Back
                </button>
                <div style="display:flex;gap:10px">
                    <button onclick="ccSavePrefs()" style="padding:10px 22px;background:linear-gradient(135deg,#6366f1,#7c3aed);color:#fff;font-size:13px;font-weight:600;border:none;border-radius:12px;cursor:pointer">Save Preferences</button>
                    <button onclick="ccAcceptAll()" style="padding:10px 22px;background:#fff;color:#374151;font-size:13px;font-weight:600;border:1.5px solid #d1d5db;border-radius:12px;cursor:pointer">Accept All</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function(){
    var _on={};
    function _gc(n){var m=document.cookie.match(new RegExp('(?:^|; )'+n.replace(/([.*+?^=!:${}()|[\]\/\\])/g,'\\$1')+'=([^;]*)'));return m?decodeURIComponent(m[1]):null;}
    function _sc(n,v,d){var e=new Date();e.setTime(e.getTime()+d*86400000);document.cookie=n+'='+encodeURIComponent(v)+';expires='+e.toUTCString()+';path=/;SameSite=Lax';}
    function _banner(){return document.getElementById('cc-banner');}
    function _modal(){return document.getElementById('cc-modal');}

    window.ccToggleBtn=function(btn,onColor){
        var dot=btn.querySelector('span');
        if(!dot)return;
        if(btn.dataset.on==='1'){btn.dataset.on='0';btn.style.background='#d1d5db';dot.style.transform='translateX(0)';}
        else{btn.dataset.on='1';btn.style.background=onColor;dot.style.transform='translateX(18px)';}
    };
    window.ccShowModal=function(){var b=_banner(),m=_modal();if(b)b.style.display='none';if(m)m.style.display='block';};
    window.ccHideModal=function(){var m=_modal(),b=_banner();if(m)m.style.display='none';if(b&&!_gc('cookie_consent'))b.style.display='block';};
    function _save(p){
        _sc('cookie_consent',JSON.stringify(p),365);
        _sc('cookie_essential','1',365);
        _sc('cookie_performance',p.performance?'1':'0',365);
        _sc('cookie_functional',p.functional?'1':'0',365);
        _sc('cookie_marketing',p.marketing?'1':'0',365);
        var b=_banner(),m=_modal();
        if(b)b.style.display='none';if(m)m.style.display='none';
    }
    window.ccAcceptAll=function(){_save({essential:true,performance:true,functional:true,marketing:true});};
    window.ccEssentialOnly=function(){_save({essential:true,performance:false,functional:false,marketing:false});};
    window.ccSavePrefs=function(){
        function _on(id){var b=document.getElementById(id);return b?b.dataset.on==='1':true;}
        _save({essential:true,performance:_on('cc-perf-btn'),functional:_on('cc-func-btn'),marketing:_on('cc-mkt-btn')});
    };
    function _init(){
        var perf=document.getElementById('cc-perf-btn'),func=document.getElementById('cc-func-btn');
        if(perf){perf.dataset.on='1';var d=perf.querySelector('span');if(d)d.style.transform='translateX(18px)';}
        if(func){func.dataset.on='1';var d2=func.querySelector('span');if(d2)d2.style.transform='translateX(18px)';}
        var mkt=document.getElementById('cc-mkt-btn');if(mkt)mkt.dataset.on='0';
        if(!_gc('cookie_consent')){setTimeout(function(){var b=_banner();if(b)b.style.display='block';},900);}
        var m=_modal();
        if(m)m.addEventListener('click',function(e){if(e.target===m)ccHideModal();});
    }
    if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',_init);}else{_init();}
})();
</script>