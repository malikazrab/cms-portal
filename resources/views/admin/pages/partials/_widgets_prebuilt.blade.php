{{--
|--------------------------------------------------------------------------
| _widgets_prebuilt.blade.php  — PRE-BUILT SECTION WIDGETS ADDON
|--------------------------------------------------------------------------
| 30 essential pre-built sections:
|   Headers, Heroes, Features, Footers, CTA, Testimonials, Stats, etc.
|
| HOW TO INTEGRATE into _scripts.blade.php:
|
|   STEP 1 — Add category entries to widgetCategories[]
|   STEP 2 — Add default settings to getDefaultSettings() map
|   STEP 3 — Add render functions to renders{} inside renderWidget()
|   STEP 4 — Add settings panels to panels{} inside renderSettingsPanel()
|   STEP 5 — Add icon entries to getWidgetIcon() map
|
| Each section below is labelled with which STEP it belongs to.
|--------------------------------------------------------------------------
--}}

{{-- ══════════════════════════════════════════════════════════════════════
     STEP 1  — ADD THESE OBJECTS TO widgetCategories[]
══════════════════════════════════════════════════════════════════════ --}}
{{--
{
  name: 'Headers & Nav', open: false,
  widgets: [
    { type: 'nav-simple',       label: 'Simple Nav',      icon: 'fa-bars' },
    { type: 'nav-centered',     label: 'Centered Nav',    icon: 'fa-align-center' },
    { type: 'nav-saas',         label: 'SaaS Nav',        icon: 'fa-rocket' },
    { type: 'nav-ecommerce',    label: 'Shop Nav',        icon: 'fa-shopping-bag' },
    { type: 'announcement-bar', label: 'Announcement Bar',icon: 'fa-bullhorn' },
  ]
},
{
  name: 'Hero Sections', open: false,
  widgets: [
    { type: 'hero-split',       label: 'Hero Split',      icon: 'fa-columns' },
    { type: 'hero-centered',    label: 'Hero Centered',   icon: 'fa-align-center' },
    { type: 'hero-image-bg',    label: 'Hero Image BG',   icon: 'fa-image' },
    { type: 'hero-gradient',    label: 'Hero Gradient',   icon: 'fa-paint-brush' },
    { type: 'hero-minimal',     label: 'Hero Minimal',    icon: 'fa-minus-square' },
    { type: 'hero-saas',        label: 'Hero SaaS',       icon: 'fa-desktop' },
    { type: 'hero-agency',      label: 'Hero Agency',     icon: 'fa-bold' },
  ]
},
{
  name: 'Features', open: false,
  widgets: [
    { type: 'features-grid',    label: 'Features Grid',   icon: 'fa-th' },
    { type: 'features-list',    label: 'Features List',   icon: 'fa-list' },
    { type: 'features-icons',   label: 'Icon Features',   icon: 'fa-icons' },
    { type: 'features-numbered',label: 'Steps Numbered',  icon: 'fa-list-ol' },
    { type: 'features-cards',   label: 'Feature Cards',   icon: 'fa-id-card' },
  ]
},
{
  name: 'Content Sections', open: false,
  widgets: [
    { type: 'content-two-col',  label: 'Two Column',      icon: 'fa-columns' },
    { type: 'content-stats',    label: 'Stats Row',       icon: 'fa-chart-bar' },
    { type: 'content-timeline', label: 'Timeline',        icon: 'fa-stream' },
    { type: 'content-cta-strip',label: 'CTA Strip',       icon: 'fa-hand-point-right' },
    { type: 'content-faq',      label: 'FAQ Section',     icon: 'fa-question-circle' },
  ]
},
{
  name: 'Social Proof', open: false,
  widgets: [
    { type: 'testimonials-grid',label: 'Testimonials Grid',icon: 'fa-star' },
    { type: 'logos-row',        label: 'Logos / Brands',  icon: 'fa-building' },
    { type: 'rating-summary',   label: 'Rating Summary',  icon: 'fa-star-half-alt' },
  ]
},
{
  name: 'Footers', open: false,
  widgets: [
    { type: 'footer-simple',    label: 'Simple Footer',   icon: 'fa-window-minimize' },
    { type: 'footer-four-col',  label: '4-Col Footer',    icon: 'fa-th-large' },
    { type: 'footer-dark',      label: 'Dark Footer',     icon: 'fa-moon' },
    { type: 'footer-minimal',   label: 'Minimal Footer',  icon: 'fa-minus' },
  ]
},
--}}

{{-- ══════════════════════════════════════════════════════════════════════
     STEP 2  — ADD TO getDefaultSettings() map
══════════════════════════════════════════════════════════════════════ --}}
{{--
'nav-simple':        { ...base, logo: 'MySite', links: 'Home,About,Services,Contact', ctaText: 'Get Started', ctaLink: '#', bgColor: '#ffffff', textColor: '#1e293b' },
'nav-centered':      { ...base, logo: 'MySite', links: 'Home,About,Portfolio,Blog,Contact', bgColor: '#ffffff' },
'nav-saas':          { ...base, logo: 'SaaSApp', links: 'Features,Pricing,Docs,Blog', loginText: 'Log In', ctaText: 'Start Free Trial', bgColor: '#ffffff' },
'nav-ecommerce':     { ...base, logo: 'MyShop', links: 'Home,Shop,Sale,About', bgColor: '#ffffff', cartCount: 3 },
'announcement-bar':  { ...base, text: '🎉 Special Offer: 20% off all plans this week!', bgColor: '#0ea5e9', textColor: '#ffffff', ctaText: 'Grab Deal', ctaLink: '#' },
'hero-split':        { ...base, headline: 'Build Faster,\nLaunch Smarter', subtext: 'The all-in-one platform that helps your team ship products 10x faster with confidence.', ctaText: 'Start Free Trial', ctaLink: '#', ctaSecondary: 'Watch Demo', imageUrl: 'https://picsum.photos/600/500?random=101', bgColor: '#f8fafc', pt: 80, pb: 80 },
'hero-centered':     { ...base, badge: '🚀 New Launch', headline: 'The Future of Web Design\nIs Here Today', subtext: 'Create stunning websites without writing a single line of code. Drag, drop, and publish in minutes.', ctaText: 'Get Started Free', ctaLink: '#', ctaSecondary: 'See How It Works', bgColor: '#ffffff', pt: 100, pb: 100 },
'hero-image-bg':     { ...base, headline: 'Create Something\nAmazing Today', subtext: 'Join 50,000+ creators building the web of tomorrow.', ctaText: 'Start Building', imageUrl: 'https://picsum.photos/1600/800?random=102', overlay: '0.5', pt: 120, pb: 120 },
'hero-gradient':     { ...base, headline: 'Scale Your Business\nWithout Limits', subtext: 'Powerful tools for modern teams. Simple enough for everyone.', ctaText: 'Try For Free', ctaSecondary: 'Book a Demo', gradientFrom: '#667eea', gradientTo: '#764ba2', pt: 100, pb: 100 },
'hero-minimal':      { ...base, headline: 'Design Without\nBoundaries', subtext: 'Simple. Beautiful. Powerful.', ctaText: 'Get Started', pt: 120, pb: 120 },
'hero-saas':         { ...base, badge: 'Now in Beta', headline: 'Your Team\'s New\nCommand Center', subtext: 'Manage projects, track progress, and collaborate seamlessly — all in one place.', ctaText: 'Start Free', ctaSecondary: 'Watch Demo', imageUrl: 'https://picsum.photos/700/450?random=103', bgColor: '#0f172a', pt: 80, pb: 80 },
'hero-agency':       { ...base, eyebrow: 'Digital Agency', headline: 'We Build\nExperiences\nThat Matter', ctaText: 'Our Work', ctaSecondary: 'Contact Us', bgColor: '#0f172a', accentColor: '#f59e0b', pt: 100, pb: 100 },
'features-grid':     { ...base, headline: 'Everything You Need', subtext: 'Powerful features built for modern teams.', features: [{icon:'fa-bolt',title:'Lightning Fast',text:'Optimized for speed from day one.'},{icon:'fa-shield-alt',title:'Secure by Default',text:'Enterprise-grade security built in.'},{icon:'fa-sync',title:'Always Synced',text:'Real-time updates across all devices.'},{icon:'fa-chart-line',title:'Smart Analytics',text:'Deep insights to grow faster.'},{icon:'fa-users',title:'Team Ready',text:'Built for collaboration at any scale.'},{icon:'fa-plug',title:'100+ Integrations',text:'Connects with your favourite tools.'}], bgColor: '#ffffff', pt: 80, pb: 80 },
'features-list':     { ...base, headline: 'Built for scale', items: [{title:'Blazing Performance',text:'Our CDN-backed infrastructure handles millions of requests per second without breaking a sweat.',imageUrl:'https://picsum.photos/500/350?random=104'},{title:'Developer Friendly',text:'Clean APIs, great documentation, and SDKs for every major language. You\'ll be up and running in minutes.',imageUrl:'https://picsum.photos/500/350?random=105'},{title:'Analytics That Matter',text:'Beautiful dashboards showing the metrics that actually move the needle for your business.',imageUrl:'https://picsum.photos/500/350?random=106'}], pt: 80, pb: 80 },
'features-icons':    { ...base, headline: 'Why Teams Love Us', subtext: 'Simple, powerful, and designed with your team in mind.', items: [{icon:'fa-rocket',label:'Fast Deploy'},{icon:'fa-lock',label:'Secure'},{icon:'fa-cloud',label:'Cloud Native'},{icon:'fa-mobile-alt',label:'Mobile First'},{icon:'fa-headset',label:'24/7 Support'},{icon:'fa-code',label:'Open API'}], bgColor: '#f8fafc', pt: 60, pb: 60 },
'features-numbered': { ...base, headline: 'How It Works', subtext: 'Get started in three simple steps.', steps: [{title:'Create Account',text:'Sign up free in under 60 seconds. No credit card required.'},{title:'Set Up Project',text:'Use our guided wizard to configure your workspace in minutes.'},{title:'Launch & Grow',text:'Go live and start tracking results with powerful built-in analytics.'}], bgColor: '#ffffff', pt: 80, pb: 80 },
'features-cards':    { ...base, headline: 'Core Features', cards: [{icon:'fa-layer-group',title:'Drag & Drop',text:'Build pages visually with our intuitive editor.',color:'#0ea5e9'},{icon:'fa-paint-roller',title:'Custom Themes',text:'Choose from 100+ professionally designed themes.',color:'#8b5cf6'},{icon:'fa-database',title:'CMS Built-in',text:'Manage content with a powerful built-in CMS.',color:'#f59e0b'},{icon:'fa-chart-pie',title:'Analytics',text:'Track every visitor, click and conversion.',color:'#22c55e'}], bgColor: '#ffffff', pt: 80, pb: 80 },
'content-two-col':   { ...base, eyebrow: 'About Us', headline: 'We\'re on a Mission to\nSimplify the Web', text: '<p>Founded in 2020, we\'ve helped over 50,000 businesses launch beautiful websites without needing a developer.</p><p style="margin-top:12px">Our team of 40 engineers, designers, and customer success specialists work every day to make your online presence shine.</p>', ctaText: 'Our Story', imageUrl: 'https://picsum.photos/600/450?random=107', imagePosition: 'right', pt: 80, pb: 80 },
'content-stats':     { ...base, headline: 'Trusted by teams worldwide', stats: [{value:'50K+',label:'Active Users'},{value:'98%',label:'Uptime SLA'},{value:'4.9★',label:'Average Rating'},{value:'150+',label:'Countries'}], bgColor: '#0ea5e9', textColor: '#ffffff', pt: 60, pb: 60 },
'content-timeline':  { ...base, headline: 'Our Journey', events: [{year:'2020',title:'Company Founded',text:'Started in a garage with 3 people and a big dream.'},{year:'2021',title:'First 1,000 Users',text:'Reached our first milestone in just 6 months.'},{year:'2022',title:'Series A Funding',text:'Raised $5M to accelerate product development.'},{year:'2023',title:'Global Expansion',text:'Opened offices in Europe and Asia-Pacific.'},{year:'2024',title:'100K Users',text:'Crossed 100,000 active users milestone.'}], bgColor: '#f8fafc', pt: 80, pb: 80 },
'content-cta-strip': { ...base, headline: 'Ready to get started?', subtext: 'Join 50,000+ businesses already growing with us.', ctaText: 'Start Free Trial', ctaLink: '#', ctaSecondary: 'Talk to Sales', bgColor: '#0ea5e9', pt: 60, pb: 60 },
'content-faq':       { ...base, headline: 'Frequently Asked Questions', subtext: 'Everything you need to know. Can\'t find the answer? Contact our team.', items: [{q:'Is there a free plan?',a:'Yes! Our free plan includes everything you need to get started with up to 3 projects and 1GB of storage.'},{q:'Can I upgrade or downgrade anytime?',a:'Absolutely. You can change your plan at any time and we\'ll prorate the difference automatically.'},{q:'Do you offer refunds?',a:'We offer a 30-day money-back guarantee on all paid plans. No questions asked.'},{q:'Is my data safe?',a:'Your data is encrypted at rest and in transit. We\'re SOC2 Type II certified and GDPR compliant.'},{q:'Do you have an API?',a:'Yes! Our REST API lets you integrate with any tool. Full documentation is available for all plan levels.'}], bgColor: '#ffffff', pt: 80, pb: 80 },
'testimonials-grid': { ...base, headline: 'Loved by thousands', subtext: 'Don\'t just take our word for it.', items: [{text:'This tool completely changed how we work. We\'ve cut our deployment time in half.',author:'Sarah Johnson',role:'CTO at TechFlow',photo:'https://i.pravatar.cc/80?img=1',rating:5},{text:'The best investment we\'ve made this year. Our team productivity is through the roof.',author:'Mike Chen',role:'Product Lead at Nexus',photo:'https://i.pravatar.cc/80?img=3',rating:5},{text:'I\'ve tried every tool out there. Nothing comes close to the simplicity and power here.',author:'Emma Davis',role:'Founder at Bloom',photo:'https://i.pravatar.cc/80?img=5',rating:5},{text:'Onboarding was seamless. We were up and running in a single afternoon.',author:'James Wilson',role:'Dev Lead at Forma',photo:'https://i.pravatar.cc/80?img=8',rating:5},{text:'Customer support alone is worth the price. They actually respond in minutes.',author:'Aisha Patel',role:'COO at Sphere',photo:'https://i.pravatar.cc/80?img=9',rating:5},{text:'We migrated from a $500/month solution and have never looked back.',author:'Tom Reeves',role:'Founder at Slate',photo:'https://i.pravatar.cc/80?img=12',rating:5}], bgColor: '#f8fafc', pt: 80, pb: 80 },
'logos-row':         { ...base, headline: 'Trusted by leading companies', logos: ['Google','Microsoft','Stripe','Shopify','Notion','Figma','Vercel','Netlify'], bgColor: '#ffffff', pt: 50, pb: 50 },
'rating-summary':    { ...base, headline: 'What our users say', average: '4.9', total: '12,500', breakdown: [{stars:5,pct:82},{stars:4,pct:12},{stars:3,pct:4},{stars:2,pct:1},{stars:1,pct:1}], bgColor: '#f8fafc', pt: 60, pb: 60 },
'footer-simple':     { ...base, logo: 'MySite', tagline: 'Building the web of tomorrow.', links: 'Home,About,Services,Blog,Contact', copyright: '© 2026 MySite. All rights reserved.', bgColor: '#1e293b', textColor: '#94a3b8', pt: 40, pb: 30 },
'footer-four-col':   { ...base, logo: 'MySite', tagline: 'The platform for modern teams.', col1Title: 'Product', col1Links: 'Features,Pricing,Changelog,Roadmap', col2Title: 'Company', col2Links: 'About,Blog,Careers,Press', col3Title: 'Legal', col3Links: 'Privacy,Terms,Cookies,Licenses', col4Title: 'Connect', col4Links: 'Twitter,LinkedIn,GitHub,Discord', copyright: '© 2026 MySite Inc.', bgColor: '#0f172a', textColor: '#94a3b8', pt: 60, pb: 40 },
'footer-dark':       { ...base, logo: 'MySite', tagline: 'The all-in-one platform for modern businesses.', col1Title: 'Platform', col1Links: 'Dashboard,Analytics,Integrations,API', col2Title: 'Resources', col2Links: 'Docs,Tutorials,Blog,Status', col3Title: 'Company', col3Links: 'About,Team,Press,Investors', ctaHeadline: 'Stay in the loop', ctaSubtext: 'Get product updates and tips delivered to your inbox.', placeholder: 'Enter your email', btnText: 'Subscribe', copyright: '© 2026 MySite. All rights reserved.', bgColor: '#020617', textColor: '#64748b', pt: 80, pb: 40 },
'footer-minimal':    { ...base, logo: 'MySite', links: 'Privacy,Terms,Contact', copyright: '© 2026 MySite', bgColor: '#f8fafc', textColor: '#64748b', pt: 30, pb: 30 },
--}}

{{-- ══════════════════════════════════════════════════════════════════════
     STEP 3  — ADD TO renders{} inside renderWidget()
══════════════════════════════════════════════════════════════════════ --}}
<script>
// ─── PASTE THESE INSIDE the renders = { ... } object in renderWidget() ───

const prebuiltRenders = {

  // ── ANNOUNCEMENT BAR ────────────────────────────────────────────────
  'announcement-bar': () => `<div ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#0ea5e9'};color:${s.textColor||'#fff'};text-align:center;padding:10px 20px;font-size:14px;font-weight:500;">
    ${s.text||'Special announcement here!'}
    ${s.ctaText?`<a href="${s.ctaLink||'#'}" style="margin-left:12px;padding:4px 12px;background:rgba(255,255,255,0.2);border-radius:4px;color:inherit;text-decoration:none;font-weight:700;">${s.ctaText} →</a>`:''}
  </div>`,

  // ── NAV SIMPLE ──────────────────────────────────────────────────────
  'nav-simple': () => {
    const links = (s.links||'Home,About,Services,Contact').split(',');
    return `<nav ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#fff'};box-shadow:0 1px 12px rgba(0,0,0,0.08);padding:16px 40px;display:flex;align-items:center;justify-content:space-between;">
      <div style="font-weight:800;font-size:20px;color:#0ea5e9;">${s.logo||'MySite'}</div>
      <div style="display:flex;gap:28px;align-items:center;">
        ${links.map(l=>`<a href="#" style="color:${s.textColor||'#475569'};text-decoration:none;font-size:15px;font-weight:500;">${l.trim()}</a>`).join('')}
        ${s.ctaText?`<a href="${s.ctaLink||'#'}" style="background:#0ea5e9;color:#fff;padding:8px 20px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;">${s.ctaText}</a>`:''}
      </div>
    </nav>`;
  },

  // ── NAV SAAS ────────────────────────────────────────────────────────
  'nav-saas': () => {
    const links = (s.links||'Features,Pricing,Docs,Blog').split(',');
    return `<nav ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#fff'};border-bottom:1px solid #f1f5f9;padding:14px 40px;display:flex;align-items:center;justify-content:space-between;">
      <div style="display:flex;align-items:center;gap:32px;">
        <div style="font-weight:800;font-size:18px;color:#0ea5e9;">${s.logo||'SaaSApp'}</div>
        <div style="display:flex;gap:24px;">
          ${links.map(l=>`<a href="#" style="color:#475569;text-decoration:none;font-size:14px;font-weight:500;">${l.trim()}</a>`).join('')}
        </div>
      </div>
      <div style="display:flex;align-items:center;gap:12px;">
        <a href="#" style="color:#475569;text-decoration:none;font-size:14px;font-weight:500;">${s.loginText||'Log In'}</a>
        <a href="#" style="background:#0ea5e9;color:#fff;padding:8px 18px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;">${s.ctaText||'Start Free Trial'}</a>
      </div>
    </nav>`;
  },

  // ── NAV ECOMMERCE ───────────────────────────────────────────────────
  'nav-ecommerce': () => {
    const links = (s.links||'Home,Shop,Sale,About').split(',');
    return `<nav ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#fff'};box-shadow:0 1px 8px rgba(0,0,0,0.06);padding:14px 40px;display:flex;align-items:center;justify-content:space-between;">
      <div style="font-weight:800;font-size:20px;">${s.logo||'MyShop'}</div>
      <div style="display:flex;gap:24px;">
        ${links.map(l=>`<a href="#" style="color:#374151;text-decoration:none;font-size:14px;font-weight:500;">${l.trim()}</a>`).join('')}
      </div>
      <div style="display:flex;align-items:center;gap:16px;">
        <i class="fas fa-search" style="color:#64748b;cursor:pointer;"></i>
        <i class="fas fa-heart" style="color:#64748b;cursor:pointer;"></i>
        <div style="position:relative;cursor:pointer;">
          <i class="fas fa-shopping-bag" style="font-size:18px;color:#374151;"></i>
          <span style="position:absolute;top:-8px;right:-8px;background:#ef4444;color:#fff;border-radius:50%;width:18px;height:18px;font-size:10px;font-weight:700;display:flex;align-items:center;justify-content:center;">${s.cartCount||0}</span>
        </div>
      </div>
    </nav>`;
  },

  // ── HERO SPLIT ──────────────────────────────────────────────────────
  'hero-split': () => `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#f8fafc'};padding:${s.pt||80}px 40px ${s.pb||80}px;">
    <div style="max-width:1200px;margin:0 auto;display:flex;align-items:center;gap:60px;flex-wrap:wrap;">
      <div style="flex:1;min-width:280px;">
        <h1 style="font-size:52px;font-weight:900;line-height:1.15;color:#0f172a;margin:0 0 20px;white-space:pre-line;">${(s.headline||'Build Faster,\nLaunch Smarter').replace(/\\n/g,'<br>')}</h1>
        <p style="font-size:18px;color:#64748b;line-height:1.7;margin:0 0 32px;">${s.subtext||'The all-in-one platform to ship faster.'}</p>
        <div style="display:flex;gap:12px;flex-wrap:wrap;">
          <a href="${s.ctaLink||'#'}" style="background:#0ea5e9;color:#fff;padding:14px 28px;border-radius:10px;text-decoration:none;font-size:16px;font-weight:700;">${s.ctaText||'Get Started'}</a>
          ${s.ctaSecondary?`<a href="#" style="color:#0ea5e9;padding:14px 28px;border-radius:10px;text-decoration:none;font-size:16px;font-weight:600;border:2px solid #e2e8f0;">${s.ctaSecondary}</a>`:''}
        </div>
      </div>
      <div style="flex:1;min-width:280px;">
        <img src="${s.imageUrl||'https://picsum.photos/600/500?random=101'}" style="width:100%;border-radius:16px;box-shadow:0 20px 60px rgba(0,0,0,0.12);">
      </div>
    </div>
  </section>`,

  // ── HERO CENTERED ───────────────────────────────────────────────────
  'hero-centered': () => `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#ffffff'};padding:${s.pt||100}px 40px ${s.pb||100}px;text-align:center;">
    <div style="max-width:800px;margin:0 auto;">
      ${s.badge?`<span style="display:inline-block;background:#eff6ff;color:#0ea5e9;padding:6px 16px;border-radius:20px;font-size:13px;font-weight:700;margin-bottom:20px;">${s.badge}</span>`:''}
      <h1 style="font-size:56px;font-weight:900;line-height:1.1;color:#0f172a;margin:0 0 20px;white-space:pre-line;">${(s.headline||'The Future of Web Design\nIs Here').replace(/\\n/g,'<br>')}</h1>
      <p style="font-size:19px;color:#64748b;line-height:1.7;margin:0 0 36px;">${s.subtext||'Create stunning websites without writing a single line of code.'}</p>
      <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
        <a href="${s.ctaLink||'#'}" style="background:#0ea5e9;color:#fff;padding:16px 32px;border-radius:10px;text-decoration:none;font-size:16px;font-weight:700;box-shadow:0 4px 14px rgba(14,165,233,0.35);">${s.ctaText||'Get Started Free'}</a>
        ${s.ctaSecondary?`<a href="#" style="color:#374151;padding:16px 32px;border-radius:10px;text-decoration:none;font-size:16px;font-weight:600;border:2px solid #e2e8f0;">${s.ctaSecondary}</a>`:''}
      </div>
    </div>
  </section>`,

  // ── HERO IMAGE BG ───────────────────────────────────────────────────
  'hero-image-bg': () => `<section ${id} class="${cls}" style="${wrapStyle};background-image:url(${s.imageUrl||'https://picsum.photos/1600/800?random=102'});background-size:cover;background-position:center;padding:${s.pt||120}px 40px ${s.pb||120}px;position:relative;text-align:center;">
    <div style="position:absolute;inset:0;background:rgba(0,0,0,${s.overlay||0.5});"></div>
    <div style="position:relative;max-width:700px;margin:0 auto;color:#fff;">
      <h1 style="font-size:54px;font-weight:900;line-height:1.15;margin:0 0 20px;">${s.headline||'Create Something Amazing'}</h1>
      <p style="font-size:18px;opacity:0.85;margin:0 0 32px;line-height:1.7;">${s.subtext||'Join 50,000+ creators building the web of tomorrow.'}</p>
      <a href="${s.ctaLink||'#'}" style="background:#0ea5e9;color:#fff;padding:16px 36px;border-radius:10px;text-decoration:none;font-size:16px;font-weight:700;display:inline-block;">${s.ctaText||'Start Building'}</a>
    </div>
  </section>`,

  // ── HERO GRADIENT ───────────────────────────────────────────────────
  'hero-gradient': () => `<section ${id} class="${cls}" style="${wrapStyle};background:linear-gradient(135deg,${s.gradientFrom||'#667eea'},${s.gradientTo||'#764ba2'});padding:${s.pt||100}px 40px ${s.pb||100}px;text-align:center;color:#fff;">
    <div style="max-width:760px;margin:0 auto;">
      <h1 style="font-size:54px;font-weight:900;line-height:1.1;margin:0 0 20px;">${s.headline||'Scale Your Business\nWithout Limits'}</h1>
      <p style="font-size:18px;opacity:0.85;line-height:1.7;margin:0 0 36px;">${s.subtext||'Powerful tools for modern teams.'}</p>
      <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
        <a href="#" style="background:#fff;color:#667eea;padding:15px 32px;border-radius:10px;text-decoration:none;font-size:16px;font-weight:700;">${s.ctaText||'Try For Free'}</a>
        ${s.ctaSecondary?`<a href="#" style="color:#fff;padding:15px 32px;border-radius:10px;text-decoration:none;font-size:16px;font-weight:600;border:2px solid rgba(255,255,255,0.5);">${s.ctaSecondary}</a>`:''}
      </div>
    </div>
  </section>`,

  // ── HERO SAAS (dark) ─────────────────────────────────────────────────
  'hero-saas': () => `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#0f172a'};padding:${s.pt||80}px 40px ${s.pb||80}px;color:#fff;">
    <div style="max-width:1200px;margin:0 auto;display:flex;align-items:center;gap:60px;flex-wrap:wrap;">
      <div style="flex:1;min-width:280px;">
        ${s.badge?`<span style="display:inline-block;background:#1e3a5f;color:#38bdf8;padding:5px 14px;border-radius:20px;font-size:12px;font-weight:700;margin-bottom:20px;">${s.badge}</span>`:''}
        <h1 style="font-size:48px;font-weight:900;line-height:1.15;margin:0 0 20px;">${s.headline||'Your Team\'s New Command Center'}</h1>
        <p style="font-size:17px;color:#94a3b8;line-height:1.7;margin:0 0 32px;">${s.subtext||'Manage projects, track progress, and collaborate.'}</p>
        <div style="display:flex;gap:12px;flex-wrap:wrap;">
          <a href="#" style="background:#0ea5e9;color:#fff;padding:14px 28px;border-radius:10px;text-decoration:none;font-size:15px;font-weight:700;">${s.ctaText||'Start Free'}</a>
          ${s.ctaSecondary?`<a href="#" style="color:#94a3b8;padding:14px 28px;border-radius:10px;text-decoration:none;font-size:15px;font-weight:600;border:1px solid #334155;">${s.ctaSecondary}</a>`:''}
        </div>
      </div>
      <div style="flex:1;min-width:280px;">
        <img src="${s.imageUrl||'https://picsum.photos/700/450?random=103'}" style="width:100%;border-radius:12px;box-shadow:0 24px 64px rgba(0,0,0,0.4);">
      </div>
    </div>
  </section>`,

  // ── HERO AGENCY ──────────────────────────────────────────────────────
  'hero-agency': () => `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#0f172a'};padding:${s.pt||100}px 60px ${s.pb||100}px;color:#fff;">
    <div style="max-width:1000px;">
      ${s.eyebrow?`<p style="font-size:13px;font-weight:700;letter-spacing:0.15em;text-transform:uppercase;color:${s.accentColor||'#f59e0b'};margin:0 0 16px;">${s.eyebrow}</p>`:''}
      <h1 style="font-size:72px;font-weight:900;line-height:1.05;margin:0 0 32px;">${(s.headline||'We Build\nExperiences\nThat Matter').replace(/\n/g,'<br>')}</h1>
      <div style="display:flex;gap:12px;">
        <a href="#" style="background:${s.accentColor||'#f59e0b'};color:#000;padding:16px 32px;border-radius:8px;text-decoration:none;font-size:16px;font-weight:800;">${s.ctaText||'Our Work'}</a>
        ${s.ctaSecondary?`<a href="#" style="color:#fff;padding:16px 32px;text-decoration:none;font-size:16px;font-weight:600;border:2px solid rgba(255,255,255,0.2);border-radius:8px;">${s.ctaSecondary}</a>`:''}
      </div>
    </div>
  </section>`,

  // ── FEATURES GRID ────────────────────────────────────────────────────
  'features-grid': () => {
    const feats = s.features || [{icon:'fa-bolt',title:'Fast',text:'Speed.'},{icon:'fa-shield-alt',title:'Secure',text:'Safe.'},{icon:'fa-sync',title:'Synced',text:'Live.'}];
    return `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#fff'};padding:${s.pt||80}px 40px ${s.pb||80}px;text-align:center;">
      <div style="max-width:1100px;margin:0 auto;">
        <h2 style="font-size:40px;font-weight:900;color:#0f172a;margin:0 0 12px;">${s.headline||'Everything You Need'}</h2>
        <p style="font-size:17px;color:#64748b;margin:0 0 56px;">${s.subtext||'Built for modern teams.'}</p>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:32px;text-align:left;">
          ${feats.map(f=>`<div style="padding:28px;border:1px solid #f1f5f9;border-radius:14px;background:#fff;transition:box-shadow 0.2s;" onmouseover="this.style.boxShadow='0 8px 30px rgba(0,0,0,0.08)'" onmouseout="this.style.boxShadow='none'">
            <div style="width:48px;height:48px;background:#eff6ff;border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:16px;">
              <i class="fas ${f.icon||'fa-star'}" style="color:#0ea5e9;font-size:20px;"></i>
            </div>
            <h3 style="font-size:17px;font-weight:700;margin:0 0 8px;color:#0f172a;">${f.title||'Feature'}</h3>
            <p style="font-size:14px;color:#64748b;margin:0;line-height:1.6;">${f.text||'Description'}</p>
          </div>`).join('')}
        </div>
      </div>
    </section>`;
  },

  // ── FEATURES LIST (alternating) ──────────────────────────────────────
  'features-list': () => {
    const items = s.items || [{title:'Feature',text:'Description.',imageUrl:'https://picsum.photos/500/350?random=104'}];
    return `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#fff'};padding:${s.pt||80}px 40px ${s.pb||80}px;">
      <div style="max-width:1100px;margin:0 auto;">
        <h2 style="font-size:40px;font-weight:900;color:#0f172a;text-align:center;margin:0 0 60px;">${s.headline||'Built for scale'}</h2>
        ${items.map((item,i)=>`<div style="display:flex;align-items:center;gap:60px;margin-bottom:80px;flex-wrap:wrap;${i%2===1?'flex-direction:row-reverse;':''}">
          <div style="flex:1;min-width:260px;">
            <h3 style="font-size:30px;font-weight:800;color:#0f172a;margin:0 0 16px;">${item.title}</h3>
            <p style="font-size:16px;color:#64748b;line-height:1.7;margin:0;">${item.text}</p>
          </div>
          <div style="flex:1;min-width:260px;">
            <img src="${item.imageUrl||'https://picsum.photos/500/350?random='+i}" style="width:100%;border-radius:16px;box-shadow:0 12px 40px rgba(0,0,0,0.1);">
          </div>
        </div>`).join('')}
      </div>
    </section>`;
  },

  // ── FEATURES ICONS ───────────────────────────────────────────────────
  'features-icons': () => {
    const items = s.items || [{icon:'fa-rocket',label:'Fast'},{icon:'fa-lock',label:'Secure'},{icon:'fa-cloud',label:'Cloud'},{icon:'fa-mobile-alt',label:'Mobile'},{icon:'fa-headset',label:'Support'},{icon:'fa-code',label:'API'}];
    return `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#f8fafc'};padding:${s.pt||60}px 40px ${s.pb||60}px;text-align:center;">
      <div style="max-width:1000px;margin:0 auto;">
        <h2 style="font-size:36px;font-weight:900;color:#0f172a;margin:0 0 10px;">${s.headline||'Why Teams Love Us'}</h2>
        <p style="color:#64748b;font-size:16px;margin:0 0 48px;">${s.subtext||''}</p>
        <div style="display:flex;gap:24px;justify-content:center;flex-wrap:wrap;">
          ${items.map(item=>`<div style="display:flex;flex-direction:column;align-items:center;gap:10px;padding:24px 20px;background:#fff;border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,0.06);min-width:110px;">
            <div style="width:52px;height:52px;background:linear-gradient(135deg,#0ea5e9,#8b5cf6);border-radius:14px;display:flex;align-items:center;justify-content:center;">
              <i class="fas ${item.icon}" style="color:#fff;font-size:20px;"></i>
            </div>
            <span style="font-size:13px;font-weight:600;color:#374151;">${item.label}</span>
          </div>`).join('')}
        </div>
      </div>
    </section>`;
  },

  // ── FEATURES NUMBERED (Steps) ────────────────────────────────────────
  'features-numbered': () => {
    const steps = s.steps || [{title:'Create Account',text:'Sign up free.'},{title:'Set Up Project',text:'Configure in minutes.'},{title:'Launch & Grow',text:'Go live today.'}];
    return `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#fff'};padding:${s.pt||80}px 40px ${s.pb||80}px;text-align:center;">
      <div style="max-width:900px;margin:0 auto;">
        <h2 style="font-size:40px;font-weight:900;color:#0f172a;margin:0 0 12px;">${s.headline||'How It Works'}</h2>
        <p style="color:#64748b;font-size:17px;margin:0 0 56px;">${s.subtext||''}</p>
        <div style="display:flex;gap:0;justify-content:center;flex-wrap:wrap;position:relative;">
          ${steps.map((step,i)=>`<div style="flex:1;min-width:200px;padding:0 20px;position:relative;">
            <div style="width:52px;height:52px;background:#0ea5e9;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:20px;font-weight:900;color:#fff;">${i+1}</div>
            ${i<steps.length-1?`<div style="position:absolute;top:26px;left:60%;width:80%;height:2px;background:#e2e8f0;"></div>`:''}
            <h3 style="font-size:17px;font-weight:700;color:#0f172a;margin:0 0 8px;">${step.title}</h3>
            <p style="font-size:14px;color:#64748b;margin:0;">${step.text}</p>
          </div>`).join('')}
        </div>
      </div>
    </section>`;
  },

  // ── FEATURES CARDS ───────────────────────────────────────────────────
  'features-cards': () => {
    const cards = s.cards || [{icon:'fa-layer-group',title:'Drag & Drop',text:'Visual editor.',color:'#0ea5e9'},{icon:'fa-paint-roller',title:'Themes',text:'100+ themes.',color:'#8b5cf6'}];
    return `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#fff'};padding:${s.pt||80}px 40px ${s.pb||80}px;">
      <div style="max-width:1100px;margin:0 auto;text-align:center;">
        <h2 style="font-size:40px;font-weight:900;color:#0f172a;margin:0 0 48px;">${s.headline||'Core Features'}</h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:24px;">
          ${cards.map(card=>`<div style="padding:32px 24px;border-radius:16px;background:#fff;border:1px solid #f1f5f9;border-top:4px solid ${card.color||'#0ea5e9'};text-align:left;transition:transform 0.2s,box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 40px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
            <i class="fas ${card.icon||'fa-star'}" style="font-size:28px;color:${card.color||'#0ea5e9'};margin-bottom:16px;display:block;"></i>
            <h3 style="font-size:18px;font-weight:700;color:#0f172a;margin:0 0 8px;">${card.title}</h3>
            <p style="font-size:14px;color:#64748b;margin:0;line-height:1.6;">${card.text}</p>
          </div>`).join('')}
        </div>
      </div>
    </section>`;
  },

  // ── CONTENT TWO COLUMN ───────────────────────────────────────────────
  'content-two-col': () => {
    const imgLeft = s.imagePosition === 'left';
    return `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#fff'};padding:${s.pt||80}px 40px ${s.pb||80}px;">
      <div style="max-width:1100px;margin:0 auto;display:flex;align-items:center;gap:60px;flex-wrap:wrap;${imgLeft?'flex-direction:row-reverse;':''}">
        <div style="flex:1;min-width:260px;">
          ${s.eyebrow?`<p style="font-size:13px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:#0ea5e9;margin:0 0 10px;">${s.eyebrow}</p>`:''}
          <h2 style="font-size:38px;font-weight:900;color:#0f172a;line-height:1.2;margin:0 0 16px;">${(s.headline||'We\'re on a Mission').replace(/\n/g,'<br>')}</h2>
          <div style="font-size:16px;color:#64748b;line-height:1.7;">${s.text||'<p>Description here.</p>'}</div>
          ${s.ctaText?`<a href="#" style="display:inline-block;margin-top:24px;background:#0ea5e9;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-size:15px;font-weight:700;">${s.ctaText} →</a>`:''}
        </div>
        <div style="flex:1;min-width:260px;">
          <img src="${s.imageUrl||'https://picsum.photos/600/450?random=107'}" style="width:100%;border-radius:16px;box-shadow:0 16px 48px rgba(0,0,0,0.1);">
        </div>
      </div>
    </section>`;
  },

  // ── CONTENT STATS ────────────────────────────────────────────────────
  'content-stats': () => {
    const stats = s.stats || [{value:'50K+',label:'Users'},{value:'99%',label:'Uptime'},{value:'4.9★',label:'Rating'},{value:'150+',label:'Countries'}];
    return `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#0ea5e9'};padding:${s.pt||60}px 40px ${s.pb||60}px;text-align:center;">
      <div style="max-width:1000px;margin:0 auto;">
        ${s.headline?`<h2 style="font-size:32px;font-weight:800;color:${s.textColor||'#fff'};margin:0 0 40px;">${s.headline}</h2>`:''}
        <div style="display:flex;gap:0;justify-content:center;flex-wrap:wrap;">
          ${stats.map((stat,i)=>`<div style="flex:1;min-width:160px;padding:20px;${i>0?'border-left:1px solid rgba(255,255,255,0.2)':''}">
            <div style="font-size:48px;font-weight:900;color:${s.textColor||'#fff'};line-height:1;">${stat.value}</div>
            <p style="font-size:14px;color:rgba(255,255,255,0.75);margin:8px 0 0;font-weight:500;">${stat.label}</p>
          </div>`).join('')}
        </div>
      </div>
    </section>`;
  },

  // ── CONTENT TIMELINE ─────────────────────────────────────────────────
  'content-timeline': () => {
    const events = s.events || [{year:'2020',title:'Founded',text:'Started the journey.'},{year:'2022',title:'Growth',text:'Reached milestones.'},{year:'2024',title:'Today',text:'Still going strong.'}];
    return `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#f8fafc'};padding:${s.pt||80}px 40px ${s.pb||80}px;">
      <div style="max-width:700px;margin:0 auto;text-align:center;">
        <h2 style="font-size:40px;font-weight:900;color:#0f172a;margin:0 0 56px;">${s.headline||'Our Journey'}</h2>
        <div style="position:relative;">
          <div style="position:absolute;left:50%;transform:translateX(-50%);top:0;bottom:0;width:2px;background:#e2e8f0;"></div>
          ${events.map((ev,i)=>`<div style="display:flex;gap:32px;align-items:flex-start;margin-bottom:40px;text-align:${i%2===0?'right':'left'};flex-direction:${i%2===0?'row-reverse':'row'};">
            <div style="flex:1;">
              <span style="font-size:12px;font-weight:700;color:#0ea5e9;">${ev.year}</span>
              <h3 style="font-size:18px;font-weight:700;color:#0f172a;margin:4px 0 6px;">${ev.title}</h3>
              <p style="font-size:14px;color:#64748b;margin:0;">${ev.text}</p>
            </div>
            <div style="width:16px;height:16px;background:#0ea5e9;border-radius:50%;flex-shrink:0;margin-top:20px;position:relative;z-index:1;box-shadow:0 0 0 4px #fff;"></div>
            <div style="flex:1;"></div>
          </div>`).join('')}
        </div>
      </div>
    </section>`;
  },

  // ── CONTENT CTA STRIP ────────────────────────────────────────────────
  'content-cta-strip': () => `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#0ea5e9'};padding:${s.pt||60}px 40px ${s.pb||60}px;text-align:center;">
    <div style="max-width:700px;margin:0 auto;">
      <h2 style="font-size:38px;font-weight:900;color:#fff;margin:0 0 12px;">${s.headline||'Ready to get started?'}</h2>
      <p style="font-size:17px;color:rgba(255,255,255,0.85);margin:0 0 32px;">${s.subtext||'Join 50,000+ businesses growing with us.'}</p>
      <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
        <a href="${s.ctaLink||'#'}" style="background:#fff;color:#0ea5e9;padding:14px 32px;border-radius:10px;text-decoration:none;font-size:16px;font-weight:800;">${s.ctaText||'Start Free Trial'}</a>
        ${s.ctaSecondary?`<a href="#" style="color:#fff;padding:14px 32px;border-radius:10px;text-decoration:none;font-size:16px;font-weight:600;border:2px solid rgba(255,255,255,0.5);">${s.ctaSecondary}</a>`:''}
      </div>
    </div>
  </section>`,

  // ── CONTENT FAQ ──────────────────────────────────────────────────────
  'content-faq': () => {
    const items = s.items || [{q:'What is this?',a:'This is our product.'},{q:'How much does it cost?',a:'We have a free plan.'}];
    return `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#fff'};padding:${s.pt||80}px 40px ${s.pb||80}px;">
      <div style="max-width:720px;margin:0 auto;text-align:center;">
        <h2 style="font-size:40px;font-weight:900;color:#0f172a;margin:0 0 12px;">${s.headline||'FAQ'}</h2>
        <p style="color:#64748b;font-size:16px;margin:0 0 48px;">${s.subtext||''}</p>
        <div style="text-align:left;space-y:0;">
          ${items.map((item,i)=>`<div style="border-bottom:1px solid #f1f5f9;">
            <button onclick="const c=this.nextElementSibling;c.style.display=c.style.display==='block'?'none':'block'" style="width:100%;text-align:left;padding:18px 0;background:none;border:none;font-size:16px;font-weight:600;color:#0f172a;cursor:pointer;display:flex;justify-content:space-between;align-items:center;">
              ${item.q} <i class="fas fa-chevron-down" style="color:#94a3b8;font-size:12px;"></i>
            </button>
            <div style="display:${i===0?'block':'none'};padding-bottom:16px;font-size:15px;color:#64748b;line-height:1.7;">${item.a}</div>
          </div>`).join('')}
        </div>
      </div>
    </section>`;
  },

  // ── TESTIMONIALS GRID ────────────────────────────────────────────────
  'testimonials-grid': () => {
    const items = s.items || [{text:'Amazing product!',author:'Jane Smith',role:'CEO',photo:'https://i.pravatar.cc/80?img=1',rating:5},{text:'Love it so much.',author:'Mike Chen',role:'Lead Dev',photo:'https://i.pravatar.cc/80?img=3',rating:5},{text:'Best tool ever.',author:'Emma Davis',role:'Founder',photo:'https://i.pravatar.cc/80?img=5',rating:5}];
    return `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#f8fafc'};padding:${s.pt||80}px 40px ${s.pb||80}px;text-align:center;">
      <div style="max-width:1100px;margin:0 auto;">
        <h2 style="font-size:40px;font-weight:900;color:#0f172a;margin:0 0 10px;">${s.headline||'Loved by thousands'}</h2>
        <p style="color:#64748b;font-size:17px;margin:0 0 48px;">${s.subtext||'Don\'t take our word for it.'}</p>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:24px;text-align:left;">
          ${items.map(item=>`<div style="background:#fff;padding:28px;border-radius:16px;box-shadow:0 2px 16px rgba(0,0,0,0.06);">
            <div style="display:flex;gap:3px;margin-bottom:12px;">${Array(item.rating||5).fill('').map(()=>'<i class="fas fa-star" style="color:#f59e0b;font-size:14px;"></i>').join('')}</div>
            <p style="font-size:15px;color:#374151;line-height:1.65;margin:0 0 20px;">"${item.text}"</p>
            <div style="display:flex;align-items:center;gap:10px;">
              <img src="${item.photo||'https://i.pravatar.cc/80?img=1'}" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
              <div>
                <p style="font-weight:700;font-size:14px;margin:0;color:#0f172a;">${item.author}</p>
                <p style="font-size:12px;color:#94a3b8;margin:0;">${item.role}</p>
              </div>
            </div>
          </div>`).join('')}
        </div>
      </div>
    </section>`;
  },

  // ── LOGOS ROW ────────────────────────────────────────────────────────
  'logos-row': () => {
    const logos = s.logos || ['Google','Microsoft','Stripe','Shopify','Notion','Figma'];
    return `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#fff'};padding:${s.pt||50}px 40px ${s.pb||50}px;text-align:center;">
      ${s.headline?`<p style="font-size:13px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:#94a3b8;margin:0 0 24px;">${s.headline}</p>`:''}
      <div style="display:flex;gap:40px;align-items:center;justify-content:center;flex-wrap:wrap;">
        ${logos.map(logo=>`<div style="font-size:22px;font-weight:900;color:#cbd5e1;letter-spacing:-1px;transition:color 0.2s;" onmouseover="this.style.color='#64748b'" onmouseout="this.style.color='#cbd5e1'">${logo}</div>`).join('')}
      </div>
    </section>`;
  },

  // ── FOOTER SIMPLE ────────────────────────────────────────────────────
  'footer-simple': () => {
    const links = (s.links||'Home,About,Services,Blog,Contact').split(',');
    return `<footer ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#1e293b'};padding:${s.pt||40}px 40px ${s.pb||30}px;">
      <div style="max-width:1100px;margin:0 auto;">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;padding-bottom:24px;border-bottom:1px solid rgba(255,255,255,0.1);">
          <div>
            <div style="font-size:20px;font-weight:800;color:#fff;">${s.logo||'MySite'}</div>
            ${s.tagline?`<p style="font-size:13px;color:${s.textColor||'#94a3b8'};margin:4px 0 0;">${s.tagline}</p>`:''}
          </div>
          <div style="display:flex;gap:24px;flex-wrap:wrap;">
            ${links.map(l=>`<a href="#" style="color:${s.textColor||'#94a3b8'};text-decoration:none;font-size:14px;">${l.trim()}</a>`).join('')}
          </div>
        </div>
        <p style="color:${s.textColor||'#64748b'};font-size:13px;margin:20px 0 0;text-align:center;">${s.copyright||'© 2026 MySite. All rights reserved.'}</p>
      </div>
    </footer>`,
  },

  // ── FOOTER FOUR COLUMN ───────────────────────────────────────────────
  'footer-four-col': () => {
    const col = (title, linksStr) => `<div>
      <h4 style="font-size:13px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#fff;margin:0 0 16px;">${title}</h4>
      ${(linksStr||'').split(',').map(l=>`<a href="#" style="display:block;color:${s.textColor||'#94a3b8'};text-decoration:none;font-size:14px;margin-bottom:8px;">${l.trim()}</a>`).join('')}
    </div>`;
    return `<footer ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#0f172a'};padding:${s.pt||60}px 40px ${s.pb||40}px;">
      <div style="max-width:1100px;margin:0 auto;">
        <div style="display:grid;grid-template-columns:2fr 1fr 1fr 1fr 1fr;gap:40px;margin-bottom:48px;flex-wrap:wrap;">
          <div>
            <div style="font-size:22px;font-weight:800;color:#fff;margin-bottom:10px;">${s.logo||'MySite'}</div>
            <p style="font-size:14px;color:${s.textColor||'#94a3b8'};line-height:1.6;">${s.tagline||'The platform for modern teams.'}</p>
          </div>
          ${col(s.col1Title||'Product', s.col1Links||'Features,Pricing,Changelog,Roadmap')}
          ${col(s.col2Title||'Company', s.col2Links||'About,Blog,Careers,Press')}
          ${col(s.col3Title||'Legal',   s.col3Links||'Privacy,Terms,Cookies')}
          ${col(s.col4Title||'Connect', s.col4Links||'Twitter,LinkedIn,GitHub')}
        </div>
        <div style="border-top:1px solid rgba(255,255,255,0.08);padding-top:24px;text-align:center;">
          <p style="color:${s.textColor||'#475569'};font-size:13px;margin:0;">${s.copyright||'© 2026 MySite Inc.'}</p>
        </div>
      </div>
    </footer>`;
  },

  // ── FOOTER DARK (with newsletter) ────────────────────────────────────
  'footer-dark': () => {
    const col = (title, linksStr) => `<div>
      <h4 style="font-size:12px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:#94a3b8;margin:0 0 14px;">${title}</h4>
      ${(linksStr||'').split(',').map(l=>`<a href="#" style="display:block;color:#64748b;text-decoration:none;font-size:13px;margin-bottom:7px;transition:color 0.15s;" onmouseover="this.style.color='#e2e8f0'" onmouseout="this.style.color='#64748b'">${l.trim()}</a>`).join('')}
    </div>`;
    return `<footer ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#020617'};padding:${s.pt||80}px 40px ${s.pb||40}px;">
      <div style="max-width:1100px;margin:0 auto;">
        <div style="display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:40px;margin-bottom:60px;">
          <div>
            <div style="font-size:22px;font-weight:800;color:#fff;margin-bottom:10px;">${s.logo||'MySite'}</div>
            <p style="font-size:14px;color:#64748b;line-height:1.6;margin:0 0 24px;">${s.tagline||'The all-in-one platform for modern businesses.'}</p>
            ${s.ctaHeadline?`<p style="font-size:13px;font-weight:600;color:#94a3b8;margin:0 0 10px;">${s.ctaHeadline}</p>
            <div style="display:flex;gap:0;max-width:280px;">
              <input placeholder="${s.placeholder||'Enter your email'}" style="flex:1;padding:10px 12px;background:#0f172a;border:1px solid #1e293b;border-right:none;border-radius:8px 0 0 8px;color:#e2e8f0;font-size:13px;outline:none;">
              <button style="padding:10px 16px;background:#0ea5e9;color:#fff;border:none;border-radius:0 8px 8px 0;font-size:13px;font-weight:600;cursor:pointer;">${s.btnText||'Go'}</button>
            </div>`:''}
          </div>
          ${col(s.col1Title||'Platform', s.col1Links||'Dashboard,Analytics,Integrations,API')}
          ${col(s.col2Title||'Resources',s.col2Links||'Docs,Tutorials,Blog,Status')}
          ${col(s.col3Title||'Company',  s.col3Links||'About,Team,Press,Investors')}
        </div>
        <div style="border-top:1px solid #0f172a;padding-top:24px;display:flex;justify-content:space-between;flex-wrap:wrap;gap:12px;">
          <p style="color:#334155;font-size:13px;margin:0;">${s.copyright||'© 2026 MySite. All rights reserved.'}</p>
          <div style="display:flex;gap:16px;">
            <a href="#" style="color:#334155;font-size:16px;"><i class="fab fa-twitter"></i></a>
            <a href="#" style="color:#334155;font-size:16px;"><i class="fab fa-github"></i></a>
            <a href="#" style="color:#334155;font-size:16px;"><i class="fab fa-linkedin"></i></a>
          </div>
        </div>
      </div>
    </footer>`;
  },

  // ── FOOTER MINIMAL ───────────────────────────────────────────────────
  'footer-minimal': () => {
    const links = (s.links||'Privacy,Terms,Contact').split(',');
    return `<footer ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#f8fafc'};padding:${s.pt||30}px 40px ${s.pb||30}px;">
      <div style="max-width:1100px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <span style="font-weight:700;color:#94a3b8;">${s.logo||'MySite'}</span>
        <p style="font-size:13px;color:${s.textColor||'#94a3b8'};margin:0;">${s.copyright||'© 2026 MySite'}</p>
        <div style="display:flex;gap:20px;">
          ${links.map(l=>`<a href="#" style="color:${s.textColor||'#94a3b8'};text-decoration:none;font-size:13px;">${l.trim()}</a>`).join('')}
        </div>
      </div>
    </footer>`;
  },

  // ── RATING SUMMARY ───────────────────────────────────────────────────
  'rating-summary': () => {
    const breakdown = s.breakdown || [{stars:5,pct:82},{stars:4,pct:12},{stars:3,pct:4},{stars:2,pct:1},{stars:1,pct:1}];
    return `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#f8fafc'};padding:${s.pt||60}px 40px ${s.pb||60}px;text-align:center;">
      <div style="max-width:500px;margin:0 auto;">
        <h2 style="font-size:36px;font-weight:900;color:#0f172a;margin:0 0 8px;">${s.headline||'What our users say'}</h2>
        <div style="font-size:64px;font-weight:900;color:#f59e0b;line-height:1;margin:24px 0 4px;">${s.average||'4.9'}</div>
        <div style="display:flex;justify-content:center;gap:4px;margin-bottom:6px;">${Array(5).fill('').map(()=>'<i class="fas fa-star" style="color:#f59e0b;font-size:22px;"></i>').join('')}</div>
        <p style="color:#94a3b8;font-size:14px;margin:0 0 32px;">Based on ${s.total||'12,500'} reviews</p>
        <div style="space-y:8px;">
          ${breakdown.map(row=>`<div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
            <span style="font-size:13px;color:#64748b;width:32px;">${row.stars}★</span>
            <div style="flex:1;height:8px;background:#e2e8f0;border-radius:4px;overflow:hidden;">
              <div style="width:${row.pct}%;height:100%;background:#f59e0b;border-radius:4px;"></div>
            </div>
            <span style="font-size:12px;color:#94a3b8;width:32px;">${row.pct}%</span>
          </div>`).join('')}
        </div>
      </div>
    </section>`;
  },

};
// ─── END prebuiltRenders ─────────────────────────────────────────────────
// Merge: Object.assign(renders, prebuiltRenders); inside renderWidget()
</script>
