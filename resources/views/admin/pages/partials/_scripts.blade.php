{{--
|--------------------------------------------------------------------------
| Partial: _scripts.blade.php
| Path:    resources/views/admin/pages/partials/_scripts.blade.php
|--------------------------------------------------------------------------
| The complete Alpine.js pageBuilderV5() component.
|
| Sections (search for the === dividers to jump):
|
|   STATE          — reactive data properties
|   INIT           — init(), initSortable()
|   WIDGET DEFS    — widgetCategories[], getDefaultSettings()
|   RENDERING      — renderWidget(), renderChildren()
|   SETTINGS PANEL — renderSettingsPanel() + field() helper
|   CORE OPS       — create / select / find / delete / duplicate / copy / paste / move
|   DRAG & DROP    — startDragFromLibrary(), onCanvasDragOver(), dropOnCanvas()
|   UNDO / REDO    — pushHistory(), undo(), redo()
|   CONTEXT MENU   — openContextMenu(), showCanvasContextMenu()
|   MEDIA LIBRARY  — openMediaLibrary(), confirmMedia(), uploadMediaImage(), addMediaUrl()
|   AI ASSISTANT   — openAIForWidget(), generateAIText(), insertAIText()
|   TEMPLATES      — saveTemplate(), loadTemplate(), deleteTemplate(), storage helpers
|   REVISIONS      — saveRevision(), restoreRevision(), storage helpers
|   SAVE / LOAD    — savePage(), loadFromStorage(), autoSave(), publishPage(), markDirty()
|   EXPORT/IMPORT  — exportJSON(), importJSON()
|   KEYBOARD       — handleKeydown(), handleBeforeUnload()
|   DARK MODE      — toggleDarkMode()
|   CANVAS STYLES  — canvasContainerStyle(), getPageBgStyle()
|   SEO HELPERS    — countImages(), countHeadings()
|   A11Y CHECK     — runA11yCheck()
|   PAGE VERSIONS  — saveVersion(), loadVersion(), storage helpers
|   WIDGET FILTER  — filteredWidgetCategories()
|   WIDGET ICON    — getWidgetIcon()
|   WIDGET HELPERS — addAccordionItem(), addTabItem(), updateFeatures()
|   TOAST          — showToast(), removeToast()
|--------------------------------------------------------------------------
--}}

<script>
function pageBuilderV5(mode = 'page', initialHeader = null, availableMenus = []) {
  return {

    // =====================================================================
    // STATE
    // =====================================================================
    builderMode: mode,
    initialHeader: initialHeader,
    availableMenus: availableMenus,
    headerName: initialHeader?.name ?? 'New Header',
    isDefault: initialHeader?.is_default ?? false,
    pageSettings: initialHeader?.content?.settings ?? { backgroundColor: '#ffffff', containerWidth: 'full', paddingTop: 10, paddingBottom: 10, paddingLeft: 20, paddingRight: 20 },
    components: [],
    selectedId: null,
    darkMode: false,
    previewMode: 'desktop',
    livePreview: false,
    canvasZoom: 100,
    leftTab: 'widgets',
    rightTab: 'content',
    widgetSearch: '',
    isDirty: false,
    autoSaveIndicator: false,
    dragWidget: null,
    clipboard: null,
    snapGrid: false,

    // Modals
    showMediaLibrary: false,
    showAIModal: false,
    showTemplatesModal: false,
    showRevisionsModal: false,
    showShortcutsModal: false,
    showA11yModal: false,
    showVersionsModal: false,
    mediaCallback: null,
    selectedMedia: null,
    mediaUrlInput: '',
    aiPrompt: '',
    aiResult: '',
    aiLoading: false,
    aiTargetWidget: null,
    newTemplateName: '',
    newVersionName: '',

    // Collections
    templates: [],
    revisions: [],
    toasts: [],
    undoStack: [],
    redoStack: [],
    a11yIssues: [],
    pageVersions: [],
    contextMenu: { show: false, x: 0, y: 0, widgetId: null },

    mediaImages: [
      'https://picsum.photos/400/300?random=1',
      'https://picsum.photos/400/300?random=2',
      'https://picsum.photos/400/300?random=3',
      'https://picsum.photos/400/300?random=4',
      'https://picsum.photos/400/300?random=5',
      'https://picsum.photos/400/300?random=6',
      'https://picsum.photos/800/400?random=7',
      'https://picsum.photos/400/400?random=8',
    ],

    // SEO
    seoData: { title: 'My Page', meta: '' },

    // Global Styles
    globalStyles: {
      primaryColor:   '#0ea5e9',
      secondaryColor: '#8b5cf6',
      accentColor:    '#f59e0b',
      fontFamily:     'Inter, sans-serif',
      bgColor:        '#ffffff',
      bgImage:        '',
      bgSize:         'cover',
    },

    shortcuts: [
      { key: 'Ctrl+S',  desc: 'Save Page' },
      { key: 'Ctrl+Z',  desc: 'Undo' },
      { key: 'Ctrl+Y',  desc: 'Redo' },
      { key: 'Ctrl+C',  desc: 'Copy Widget' },
      { key: 'Ctrl+V',  desc: 'Paste Widget' },
      { key: 'Delete',  desc: 'Delete Widget' },
      { key: 'Ctrl+D',  desc: 'Duplicate Widget' },
      { key: 'Ctrl+P',  desc: 'Toggle Preview' },
      { key: '?',       desc: 'Show Shortcuts' },
    ],

    // =====================================================================
    // WIDGET CATEGORIES
    // =====================================================================
    widgetCategories: [
      {
        name: 'Layout', open: true,
        widgets: [
          { type: 'section',   label: 'Section',   icon: 'fa-square' },
          { type: 'container', label: 'Container', icon: 'fa-box' },
          { type: 'columns',   label: 'Columns',   icon: 'fa-columns' },
          { type: 'spacer',    label: 'Spacer',    icon: 'fa-arrows-alt-v' },
          { type: 'divider',   label: 'Divider',   icon: 'fa-minus' },
        ]
      },
      {
        name: 'Basic', open: true,
        widgets: [
          { type: 'heading',   label: 'Heading',   icon: 'fa-heading' },
          { type: 'paragraph', label: 'Paragraph', icon: 'fa-paragraph' },
          { type: 'button',    label: 'Button',    icon: 'fa-hand-pointer' },
          { type: 'image',     label: 'Image',     icon: 'fa-image' },
          { type: 'video',     label: 'Video',     icon: 'fa-video' },
          { type: 'icon',      label: 'Icon',      icon: 'fa-star' },
          { type: 'icon-list', label: 'Icon List', icon: 'fa-list-ul' },
        ]
      },
      {
        name: 'Content', open: false,
        widgets: [
          { type: 'testimonial',    label: 'Testimonial',    icon: 'fa-quote-right' },
          { type: 'team-member',    label: 'Team Member',    icon: 'fa-user-tie' },
          { type: 'pricing',        label: 'Pricing',        icon: 'fa-tag' },
          { type: 'accordion',      label: 'Accordion',      icon: 'fa-layer-group' },
          { type: 'tabs',           label: 'Tabs',           icon: 'fa-folder' },
          { type: 'counter',        label: 'Counter',        icon: 'fa-sort-numeric-up' },
          { type: 'progress-bar',   label: 'Progress Bar',   icon: 'fa-tasks' },
          { type: 'circle-progress',label: 'Circle Progress',icon: 'fa-circle-notch' },
          { type: 'countdown',      label: 'Countdown',      icon: 'fa-clock' },
        ]
      },
      {
        name: 'Media', open: false,
        widgets: [
          { type: 'image-carousel', label: 'Carousel',     icon: 'fa-images' },
          { type: 'before-after',   label: 'Before/After', icon: 'fa-adjust' },
          { type: 'lottie',         label: 'Lottie',       icon: 'fa-film' },
          { type: 'google-maps',    label: 'Maps',         icon: 'fa-map-marker-alt' },
        ]
      },
      {
        name: 'Dynamic', open: false,
        widgets: [
          { type: 'post-loop',    label: 'Post Loop',    icon: 'fa-rss' },
          { type: 'post-meta',    label: 'Post Meta',    icon: 'fa-info' },
          { type: 'author-box',   label: 'Author Box',   icon: 'fa-user' },
          { type: 'custom-field', label: 'Custom Field', icon: 'fa-database' },
        ]
      },
      {
        name: 'Forms', open: false,
        widgets: [
          { type: 'contact-form',   label: 'Contact Form', icon: 'fa-envelope' },
          { type: 'subscribe-form', label: 'Subscribe',    icon: 'fa-bell' },
          { type: 'search-form',    label: 'Search',       icon: 'fa-search' },
          { type: 'raw-html',       label: 'Raw HTML',     icon: 'fa-code' },
        ]
      },
      {
        name: 'UI Elements', open: false,
        widgets: [
          { type: 'alert-box',    label: 'Alert Box',     icon: 'fa-exclamation-circle' },
          { type: 'breadcrumbs',  label: 'Breadcrumbs',   icon: 'fa-chevron-right' },
          { type: 'table',        label: 'Table',         icon: 'fa-table' },
          { type: 'modal-trigger',label: 'Modal/Popup',   icon: 'fa-window-restore' },
          { type: 'form-advanced',label: 'Advanced Form', icon: 'fa-wpforms' },
        ]
      },

      // ── PRE-BUILT SECTION CATEGORIES ─────────────────────────────
      { name: 'Headers & Nav', open: false, widgets: [
          { type: 'announcement-bar', label: 'Announcement Bar', icon: 'fa-bullhorn' },
          { type: 'nav-simple',       label: 'Simple Nav',       icon: 'fa-bars' },
          { type: 'nav-centered',     label: 'Centered Nav',     icon: 'fa-align-center' },
          { type: 'nav-saas',         label: 'SaaS Nav',         icon: 'fa-rocket' },
          { type: 'nav-ecommerce',    label: 'Shop Nav',         icon: 'fa-shopping-bag' },
      ]},
      { name: 'Hero Sections', open: false, widgets: [
          { type: 'hero-split',    label: 'Hero Split',    icon: 'fa-columns' },
          { type: 'hero-centered', label: 'Hero Centered', icon: 'fa-align-center' },
          { type: 'hero-image-bg', label: 'Hero Image BG', icon: 'fa-image' },
          { type: 'hero-gradient', label: 'Hero Gradient', icon: 'fa-paint-brush' },
          { type: 'hero-minimal',  label: 'Hero Minimal',  icon: 'fa-minus-square' },
          { type: 'hero-saas',     label: 'Hero SaaS',     icon: 'fa-desktop' },
          { type: 'hero-agency',   label: 'Hero Agency',   icon: 'fa-bold' },
      ]},
      { name: 'Features', open: false, widgets: [
          { type: 'features-grid',     label: 'Features Grid',  icon: 'fa-th' },
          { type: 'features-list',     label: 'Features List',  icon: 'fa-list' },
          { type: 'features-icons',    label: 'Icon Features',  icon: 'fa-icons' },
          { type: 'features-numbered', label: 'Steps',          icon: 'fa-list-ol' },
          { type: 'features-cards',    label: 'Feature Cards',  icon: 'fa-id-card' },
      ]},
      { name: 'Content Sections', open: false, widgets: [
          { type: 'content-two-col',   label: 'Two Column',   icon: 'fa-columns' },
          { type: 'content-stats',     label: 'Stats Row',    icon: 'fa-chart-bar' },
          { type: 'content-timeline',  label: 'Timeline',     icon: 'fa-stream' },
          { type: 'content-cta-strip', label: 'CTA Strip',    icon: 'fa-hand-point-right' },
          { type: 'content-faq',       label: 'FAQ Section',  icon: 'fa-question-circle' },
          { type: 'newsletter-inline', label: 'Newsletter',   icon: 'fa-at' },
      ]},
      { name: 'Social Proof', open: false, widgets: [
          { type: 'testimonials-grid', label: 'Testimonials',   icon: 'fa-star' },
          { type: 'logos-row',         label: 'Logos Row',      icon: 'fa-building' },
          { type: 'rating-summary',    label: 'Rating Summary', icon: 'fa-star-half-alt' },
      ]},
      { name: 'Pricing & Team', open: false, widgets: [
          { type: 'pricing-three-col', label: 'Pricing Table',  icon: 'fa-tag' },
          { type: 'team-grid',         label: 'Team Grid',      icon: 'fa-users' },
          { type: 'contact-split',     label: 'Contact Split',  icon: 'fa-envelope-open' },
          { type: 'portfolio-grid',    label: 'Portfolio Grid', icon: 'fa-th-large' },
      ]},
      { name: 'Footers', open: false, widgets: [
          { type: 'footer-simple',   label: 'Simple Footer',  icon: 'fa-window-minimize' },
          { type: 'footer-four-col', label: '4-Col Footer',   icon: 'fa-th-large' },
          { type: 'footer-dark',     label: 'Dark Footer',    icon: 'fa-moon' },
          { type: 'footer-minimal',  label: 'Minimal Footer', icon: 'fa-minus' },
      ]},
    ],

    // =====================================================================
    // INIT
    // =====================================================================
    init() {
      this.darkMode = localStorage.getItem('builder_dark') === 'true';
      this.snapGrid = localStorage.getItem('builder_snap') === 'true';
      this.loadFromStorage();
      this.loadTemplates();
      this.loadRevisions();
      this.loadVersions();

      // Auto-save every 30 seconds
      setInterval(() => this.autoSave(), 30000);
      // Auto-revision snapshot every 5 minutes
      setInterval(() => this.saveRevision('Auto-snapshot'), 300000);
      // Persist snap-grid toggle
      this.$watch('snapGrid', v => localStorage.setItem('builder_snap', v));

      if (this.builderMode === 'header' && this.initialHeader) {
        this.components = (this.initialHeader.content?.widgets || []).map(w => ({
          id: Math.random().toString(36).slice(2),
          type: w.type,
          settings: Object.assign({}, this.getDefaultSettings(w.type), w.settings || {})
        }));
        this.globalStyles = Object.assign(this.globalStyles || {}, this.initialHeader.content?.globalStyles || {});
      }

      this.$nextTick(() => { this.initSortable(); });
    },

    initSortable() {
      const el = document.getElementById('sortable-canvas');
      if (!el || !window.Sortable) return;
      Sortable.create(el, {
        animation: 150,
        ghostClass: 'sortable-ghost',
        dragClass: 'sortable-drag',
        handle: '.canvas-widget',
        onEnd: (evt) => {
          const moved = this.components.splice(evt.oldIndex, 1)[0];
          this.components.splice(evt.newIndex, 0, moved);
          this.pushHistory();
          this.markDirty();
        }
      });
    },

    // =====================================================================
    // WIDGET DEFAULTS
    // =====================================================================
    getDefaultSettings(type) {
      const base = {
        paddingTop: 0, paddingBottom: 0, marginTop: 0, marginBottom: 0,
        pt: 0, pr: 0, pb: 0, pl: 0,
        mt: 0, mr: 0, mb: 0, ml: 0,
        bgColor: 'transparent', bgGradient: '',
        borderRadius: 0, borderWidth: 0, borderStyle: 'solid',
        borderColor: '#000000', shadow: '', customCss: '',
        elementId: '', cssClasses: '',
        hideDesktop: false, hideTablet: false, hideMobile: false,
        animation: '', label: '',
        width: '', height: '',
        display: '', flexDir: 'row', justifyContent: '', alignItems: '',
        flexGap: 0, flexWrap: false,
        gridCols: '', gridRows: '', gridGap: 0,
        fontWeight: '', textColor: '', lineHeight: 1.5,
        letterSpacing: 0, textTransform: '',
        hoverBg: '', hoverColor: '', hoverShadow: '', transition: 0.3,
        opacity: 1, rotate: 0, scale: 1,
      };
      const map = {
        section:         { ...base, bgColor: '#f8fafc', pt: 60, pb: 60, bgImage: '' },
        container:       { ...base, maxWidth: '1200px', bgColor: 'transparent' },
        columns:         { ...base, columnCount: 2, gap: 20, columns: [[], []] },
        spacer:          { ...base, height: 40 },
        divider:         { ...base, style: 'solid', color: '#e2e8f0', width: 100, thickness: 1, alignment: 'center' },
        heading:         { ...base, text: 'Your Heading Here', tag: 'h2', alignment: 'left', color: '#1e293b', fontSize: 36, fontWeight: '700' },
        paragraph:       { ...base, content: '<p>Click to edit this paragraph. You can add your content here and style it as needed.</p>', alignment: 'left' },
        button:          { ...base, text: 'Click Me', link: '#', bgColor: '#0ea5e9', textColor: '#ffffff', borderRadius: 8, size: 'md', variant: 'filled' },
        image:           { ...base, url: 'https://picsum.photos/800/400?random=10', alt: 'Image', width: 100, alignment: 'center', link: '' },
        video:           { ...base, url: 'https://www.youtube.com/embed/dQw4w9WgXcQ', ratio: '16/9', autoplay: false, controls: true },
        icon:            { ...base, iconClass: 'fas fa-star', size: 40, color: '#0ea5e9', link: '', alignment: 'center' },
        'icon-list':     { ...base, items: [{icon:'fas fa-check',text:'Feature one'},{icon:'fas fa-check',text:'Feature two'},{icon:'fas fa-check',text:'Feature three'}], iconColor:'#0ea5e9', iconSize:16, alignment:'left' },
        testimonial:     { ...base, text: 'This product is absolutely amazing! It has transformed the way I work.', author: 'Jane Smith', role: 'CEO, TechCorp', photo: 'https://i.pravatar.cc/80?img=1', rating: 5 },
        'team-member':   { ...base, photo: 'https://i.pravatar.cc/200?img=5', name: 'John Doe', role: 'Lead Developer', bio: 'Passionate developer with 10 years of experience.', social: {twitter:'#',linkedin:'#',github:'#'} },
        pricing:         { ...base, title: 'Pro Plan', price: '29', currency: '$', period: '/month', features: ['10 Projects','50GB Storage','Priority Support','Analytics'], buttonText: 'Get Started', highlighted: false },
        accordion:       { ...base, items: [{title:'Section 1',content:'<p>Content for section 1</p>',open:true},{title:'Section 2',content:'<p>Content for section 2</p>',open:false}] },
        tabs:            { ...base, activeTab: 0, items: [{label:'Tab 1',content:'<p>Content for tab 1</p>'},{label:'Tab 2',content:'<p>Content for tab 2</p>'}] },
        counter:         { ...base, start: 0, end: 100, duration: 2000, prefix: '', suffix: '+', label: 'Happy Clients', color: '#0ea5e9', fontSize: 48 },
        'progress-bar':  { ...base, label: 'Web Design', percentage: 75, color: '#0ea5e9', height: 12, striped: false },
        'circle-progress':{ ...base, percentage: 75, size: 120, strokeWidth: 10, color: '#0ea5e9', label: '75%' },
        countdown:       { ...base, targetDate: new Date(Date.now()+86400000*30).toISOString().slice(0,16), labelsDay:'Days',labelsHour:'Hours',labelsMin:'Minutes',labelsSec:'Seconds', color: '#0ea5e9' },
        'image-carousel':{ ...base, images: ['https://picsum.photos/800/400?random=11','https://picsum.photos/800/400?random=12','https://picsum.photos/800/400?random=13'], autoplay: true, captions: ['Slide 1','Slide 2','Slide 3'], currentSlide: 0 },
        'before-after':  { ...base, beforeUrl: 'https://picsum.photos/800/400?random=14', afterUrl: 'https://picsum.photos/800/400?random=15', sliderPos: 50 },
        lottie:          { ...base, url: '', loop: true, autoplay: true, height: 300 },
        'google-maps':   { ...base, address: 'New York, NY', height: 400 },
        'post-loop':     { ...base, columns: 3, count: 6, layout: 'grid' },
        'post-meta':     { ...base, author: true, date: true, category: true, comments: true },
        'author-box':    { ...base, photo: 'https://i.pravatar.cc/100?img=9', name: 'Author Name', bio: 'Content creator and digital strategist.' },
        'custom-field':  { ...base, fieldKey: 'custom_key', fieldValue: 'Custom Value', label: 'Custom Field' },
        'contact-form':  { ...base, title: 'Contact Us', submitText: 'Send Message', successMsg: "Thank you! We'll be in touch." },
        'subscribe-form':{ ...base, placeholder: 'Enter your email', buttonText: 'Subscribe', successMsg: "You've been subscribed!" },
        'search-form':   { ...base, placeholder: 'Search...', buttonText: 'Search' },
        'raw-html':      { ...base, code: '<div style="padding:20px;background:#f0f4f8;border-radius:8px;"><p>Your custom HTML here</p></div>' },
        'alert-box':     { ...base, type: 'info', title: 'Notice', message: 'This is an informational alert.', dismissible: true, icon: true },
        'breadcrumbs':   { ...base, items: [{label:'Home',link:'#'},{label:'Products',link:'#'},{label:'Current Page',link:''}], separator: '/' },
        'table':         { ...base, headers: ['Name','Role','Email'], rows: [['Alice','Developer','alice@example.com'],['Bob','Designer','bob@example.com'],['Carol','Manager','carol@example.com']], striped: true, bordered: true },
        'modal-trigger': { ...base, triggerText: 'Open Modal', triggerBg: '#0ea5e9', modalTitle: 'Modal Title', modalContent: '<p>Modal content goes here. You can put any HTML content.</p>', modalId: 'm_'+Math.random().toString(36).substr(2,6) },
        'form-advanced': { ...base, title: 'Contact Form', fields: [{type:'text',label:'Full Name',required:true},{type:'email',label:'Email Address',required:true},{type:'select',label:'Subject',options:['General','Support','Sales']},{type:'radio',label:'Contact Method',options:['Email','Phone']},{type:'checkbox',label:'Subscribe to newsletter'},{type:'textarea',label:'Message',required:true},{type:'file',label:'Attachment'}], submitText: 'Submit', successMsg: 'Message sent!' },

        // ── PRE-BUILT SECTION DEFAULTS ───────────────────────────────
        'announcement-bar': { ...base, text: '🎉 Special Offer: 20% off all plans this week!', bgColor: '#0ea5e9', textColor: '#ffffff', ctaText: 'Grab Deal', ctaLink: '#' },
        'nav-simple':        { ...base, logo: 'MySite', links: 'Home,About,Services,Contact', ctaText: 'Get Started', ctaLink: '#', bgColor: '#ffffff', textColor: '#475569' },
        'nav-centered':      { ...base, logo: 'MySite', links: 'Home,About,Portfolio,Blog,Contact', bgColor: '#ffffff' },
        'nav-saas':          { ...base, logo: 'SaaSApp', links: 'Features,Pricing,Docs,Blog', loginText: 'Log In', ctaText: 'Start Free Trial', bgColor: '#ffffff' },
        'nav-ecommerce':     { ...base, logo: 'MyShop', links: 'Home,Shop,Sale,About', bgColor: '#ffffff', cartCount: 3 },
        'hero-split':        { ...base, headline: 'Build Faster,\nLaunch Smarter', subtext: 'The all-in-one platform that helps your team ship products 10x faster.', ctaText: 'Start Free Trial', ctaLink: '#', ctaSecondary: 'Watch Demo', imageUrl: 'https://picsum.photos/600/500?random=101', bgColor: '#f8fafc', pt: 80, pb: 80 },
        'hero-centered':     { ...base, badge: '🚀 New Launch', headline: 'The Future of Web Design\nIs Here Today', subtext: 'Create stunning websites without writing a single line of code.', ctaText: 'Get Started Free', ctaLink: '#', ctaSecondary: 'See How It Works', bgColor: '#ffffff', pt: 100, pb: 100 },
        'hero-image-bg':     { ...base, headline: 'Create Something\nAmazing Today', subtext: 'Join 50,000+ creators building the web of tomorrow.', ctaText: 'Start Building', ctaLink: '#', imageUrl: 'https://picsum.photos/1600/800?random=102', overlay: '0.5', pt: 120, pb: 120 },
        'hero-gradient':     { ...base, headline: 'Scale Your Business\nWithout Limits', subtext: 'Powerful tools for modern teams. Simple enough for everyone.', ctaText: 'Try For Free', ctaSecondary: 'Book a Demo', gradientFrom: '#667eea', gradientTo: '#764ba2', pt: 100, pb: 100 },
        'hero-minimal':      { ...base, headline: 'Design Without\nBoundaries', subtext: 'Simple. Beautiful. Powerful.', ctaText: 'Get Started', bgColor: '#ffffff', pt: 120, pb: 120 },
        'hero-saas':         { ...base, badge: 'Now in Beta', headline: "Your Team's New\nCommand Center", subtext: 'Manage projects, track progress, and collaborate seamlessly.', ctaText: 'Start Free', ctaSecondary: 'Watch Demo', imageUrl: 'https://picsum.photos/700/450?random=103', bgColor: '#0f172a', pt: 80, pb: 80 },
        'hero-agency':       { ...base, eyebrow: 'Digital Agency', headline: 'We Build\nExperiences\nThat Matter', ctaText: 'Our Work', ctaSecondary: 'Contact Us', bgColor: '#0f172a', accentColor: '#f59e0b', pt: 100, pb: 100 },
        'features-grid':     { ...base, headline: 'Everything You Need', subtext: 'Powerful features built for modern teams.', features: [{icon:'fa-bolt',title:'Lightning Fast',text:'Optimized for speed from day one.'},{icon:'fa-shield-alt',title:'Secure by Default',text:'Enterprise-grade security built in.'},{icon:'fa-sync',title:'Always Synced',text:'Real-time updates across all devices.'},{icon:'fa-chart-line',title:'Smart Analytics',text:'Deep insights to grow faster.'},{icon:'fa-users',title:'Team Ready',text:'Built for collaboration at any scale.'},{icon:'fa-plug',title:'100+ Integrations',text:'Connects with your favourite tools.'}], bgColor: '#ffffff', pt: 80, pb: 80 },
        'features-list':     { ...base, headline: 'Built for scale', items: [{title:'Blazing Performance',text:'CDN-backed infrastructure for millions of requests.',imageUrl:'https://picsum.photos/500/350?random=104'},{title:'Developer Friendly',text:'Clean APIs and SDKs for every major language.',imageUrl:'https://picsum.photos/500/350?random=105'},{title:'Analytics That Matter',text:'Beautiful dashboards showing metrics that move the needle.',imageUrl:'https://picsum.photos/500/350?random=106'}], bgColor: '#ffffff', pt: 80, pb: 80 },
        'features-icons':    { ...base, headline: 'Why Teams Love Us', subtext: 'Simple, powerful, designed for your team.', items: [{icon:'fa-rocket',label:'Fast Deploy'},{icon:'fa-lock',label:'Secure'},{icon:'fa-cloud',label:'Cloud Native'},{icon:'fa-mobile-alt',label:'Mobile First'},{icon:'fa-headset',label:'24/7 Support'},{icon:'fa-code',label:'Open API'}], bgColor: '#f8fafc', pt: 60, pb: 60 },
        'features-numbered': { ...base, headline: 'How It Works', subtext: 'Get started in three simple steps.', steps: [{title:'Create Account',text:'Sign up free in under 60 seconds.'},{title:'Set Up Project',text:'Configure your workspace in minutes.'},{title:'Launch & Grow',text:'Go live and start tracking results.'}], bgColor: '#ffffff', pt: 80, pb: 80 },
        'features-cards':    { ...base, headline: 'Core Features', cards: [{icon:'fa-layer-group',title:'Drag & Drop',text:'Build pages visually.',color:'#0ea5e9'},{icon:'fa-paint-roller',title:'Custom Themes',text:'100+ professionally designed themes.',color:'#8b5cf6'},{icon:'fa-database',title:'CMS Built-in',text:'Manage content with a powerful CMS.',color:'#f59e0b'},{icon:'fa-chart-pie',title:'Analytics',text:'Track every visitor and conversion.',color:'#22c55e'}], bgColor: '#ffffff', pt: 80, pb: 80 },
        'content-two-col':   { ...base, eyebrow: 'About Us', headline: "We're on a Mission to\nSimplify the Web", text: '<p>Founded in 2020, we have helped over 50,000 businesses launch beautiful websites without needing a developer.</p>', ctaText: 'Our Story', imageUrl: 'https://picsum.photos/600/450?random=107', imagePosition: 'right', bgColor: '#ffffff', pt: 80, pb: 80 },
        'content-stats':     { ...base, headline: 'Trusted by teams worldwide', stats: [{value:'50K+',label:'Active Users'},{value:'98%',label:'Uptime SLA'},{value:'4.9★',label:'Average Rating'},{value:'150+',label:'Countries'}], bgColor: '#0ea5e9', textColor: '#ffffff', pt: 60, pb: 60 },
        'content-timeline':  { ...base, headline: 'Our Journey', events: [{year:'2020',title:'Company Founded',text:'Started in a garage with 3 people.'},{year:'2021',title:'First 1,000 Users',text:'Reached our first milestone.'},{year:'2022',title:'Series A Funding',text:'Raised $5M.'},{year:'2024',title:'100K Users',text:'Crossed 100,000 active users.'}], bgColor: '#f8fafc', pt: 80, pb: 80 },
        'content-cta-strip': { ...base, headline: 'Ready to get started?', subtext: 'Join 50,000+ businesses already growing with us.', ctaText: 'Start Free Trial', ctaLink: '#', ctaSecondary: 'Talk to Sales', bgColor: '#0ea5e9', pt: 60, pb: 60 },
        'content-faq':       { ...base, headline: 'Frequently Asked Questions', subtext: "Everything you need to know.", items: [{q:'Is there a free plan?',a:'Yes! Our free plan includes everything to get started.'},{q:'Can I upgrade anytime?',a:'Absolutely. Change plans anytime.'},{q:'Do you offer refunds?',a:'30-day money-back guarantee.'},{q:'Is my data safe?',a:'Encrypted at rest and in transit. SOC2 certified.'}], bgColor: '#ffffff', pt: 80, pb: 80 },
        'newsletter-inline': { ...base, headline: 'Stay in the loop', subtext: 'Get product updates delivered to your inbox.', placeholder: 'Enter your email', buttonText: 'Subscribe', bgColor: '#f8fafc', pt: 60, pb: 60 },
        'testimonials-grid': { ...base, headline: 'Loved by thousands', subtext: "Don't take our word for it.", items: [{text:'This tool changed how our team works.',author:'Sarah Johnson',role:'CTO at TechFlow',photo:'https://i.pravatar.cc/80?img=1',rating:5},{text:'Best investment we made this year.',author:'Mike Chen',role:'Product Lead',photo:'https://i.pravatar.cc/80?img=3',rating:5},{text:"Nothing comes close to this.",author:'Emma Davis',role:'Founder at Bloom',photo:'https://i.pravatar.cc/80?img=5',rating:5}], bgColor: '#f8fafc', pt: 80, pb: 80 },
        'logos-row':         { ...base, headline: 'Trusted by leading companies', logos: ['Google','Microsoft','Stripe','Shopify','Notion','Figma','Vercel'], bgColor: '#ffffff', pt: 50, pb: 50 },
        'rating-summary':    { ...base, headline: 'What our users say', average: '4.9', total: '12,500', breakdown: [{stars:5,pct:82},{stars:4,pct:12},{stars:3,pct:4},{stars:2,pct:1},{stars:1,pct:1}], bgColor: '#f8fafc', pt: 60, pb: 60 },
        'pricing-three-col': { ...base, headline: 'Simple, Transparent Pricing', subtext: 'No hidden fees. Cancel anytime.', plans: [{name:'Free',price:'0',period:'/mo',features:'3 Projects,1GB Storage,Community Support',cta:'Get Started',highlight:false},{name:'Pro',price:'29',period:'/mo',features:'Unlimited Projects,50GB Storage,Priority Support,Analytics,Custom Domain',cta:'Start Free Trial',highlight:true},{name:'Enterprise',price:'99',period:'/mo',features:'Everything in Pro,SSO & SAML,99.9% SLA,Dedicated Manager',cta:'Contact Sales',highlight:false}], bgColor: '#f8fafc', pt: 80, pb: 80 },
        'team-grid':         { ...base, headline: 'Meet the Team', subtext: 'The people behind the product.', members: [{photo:'https://i.pravatar.cc/200?img=10',name:'Alex Morgan',role:'CEO & Co-Founder',twitter:'#',linkedin:'#'},{photo:'https://i.pravatar.cc/200?img=11',name:'Jamie Reeves',role:'CTO',twitter:'#',linkedin:'#'},{photo:'https://i.pravatar.cc/200?img=12',name:'Sam Taylor',role:'Head of Design',twitter:'#',linkedin:'#'},{photo:'https://i.pravatar.cc/200?img=13',name:'Chris Kim',role:'Lead Engineer',twitter:'#',linkedin:'#'}], bgColor: '#ffffff', pt: 80, pb: 80 },
        'contact-split':     { ...base, headline: 'Get in Touch', subtext: "Have a question? We'd love to hear from you.", email: 'hello@mysite.com', phone: '+1 (555) 000-0000', address: '123 Main St, San Francisco, CA', submitText: 'Send Message', bgColor: '#ffffff', pt: 80, pb: 80 },
        'portfolio-grid':    { ...base, headline: 'Selected Work', projects: [{title:'Brand Identity',cat:'Branding',img:'https://picsum.photos/400/300?random=200'},{title:'E-Commerce App',cat:'Development',img:'https://picsum.photos/400/400?random=201'},{title:'Marketing Site',cat:'Web Design',img:'https://picsum.photos/400/280?random=202'},{title:'Mobile App UI',cat:'UI/UX',img:'https://picsum.photos/400/350?random=203'},{title:'Dashboard',cat:'Product',img:'https://picsum.photos/400/300?random=204'},{title:'Logo Suite',cat:'Branding',img:'https://picsum.photos/400/320?random=205'}], bgColor: '#ffffff', pt: 80, pb: 80 },
        'footer-simple':     { ...base, logo: 'MySite', tagline: 'Building the web of tomorrow.', links: 'Home,About,Services,Blog,Contact', copyright: '© 2026 MySite. All rights reserved.', bgColor: '#1e293b', textColor: '#94a3b8', pt: 40, pb: 30 },
        'footer-four-col':   { ...base, logo: 'MySite', tagline: 'The platform for modern teams.', col1Title: 'Product', col1Links: 'Features,Pricing,Changelog,Roadmap', col2Title: 'Company', col2Links: 'About,Blog,Careers,Press', col3Title: 'Legal', col3Links: 'Privacy,Terms,Cookies', copyright: '© 2026 MySite Inc.', bgColor: '#0f172a', textColor: '#94a3b8', pt: 60, pb: 40 },
        'footer-dark':       { ...base, logo: 'MySite', tagline: 'The all-in-one platform for modern businesses.', col1Title: 'Platform', col1Links: 'Dashboard,Analytics,API', col2Title: 'Resources', col2Links: 'Docs,Blog,Status', col3Title: 'Company', col3Links: 'About,Team,Press', ctaHeadline: 'Stay in the loop', placeholder: 'Enter your email', btnText: 'Subscribe', copyright: '© 2026 MySite. All rights reserved.', bgColor: '#020617', textColor: '#64748b', pt: 80, pb: 40 },
        'footer-minimal':    { ...base, logo: 'MySite', links: 'Privacy,Terms,Contact', copyright: '© 2026 MySite', bgColor: '#f8fafc', textColor: '#94a3b8', pt: 28, pb: 28 },
      };
      return map[type] || base;
    },

    // =====================================================================
    // WIDGET RENDERING
    // =====================================================================
    renderWidget(comp) {
      const s = comp.settings;
      const pt = s.pt !== undefined ? s.pt : (s.paddingTop||0);
      const pb = s.pb !== undefined ? s.pb : (s.paddingBottom||0);
      const pl = s.pl !== undefined ? s.pl : 0;
      const pr = s.pr !== undefined ? s.pr : 0;
      const mt = s.mt !== undefined ? s.mt : (s.marginTop||0);
      const mb = s.mb !== undefined ? s.mb : (s.marginBottom||0);
      const ml = s.ml !== undefined ? s.ml : 0;
      const mr = s.mr !== undefined ? s.mr : 0;
      const bg = s.bgGradient ? s.bgGradient : (s.bgColor||'transparent');
      const borderStyle  = s.borderWidth > 0 ? `border:${s.borderWidth}px ${s.borderStyle||'solid'} ${s.borderColor};` : '';
      const shadowStyle  = s.shadow    ? `box-shadow:${s.shadow};`   : '';
      const widthStyle   = s.width     ? `width:${s.width};`         : '';
      const heightStyle  = s.height    ? `height:${s.height};`       : '';
      const displayStyle = s.display   ? `display:${s.display};`     : '';
      const flexStyle    = (s.display==='flex'||s.display==='inline-flex') ? `flex-direction:${s.flexDir||'row'};${s.justifyContent?'justify-content:'+s.justifyContent+';':''}${s.alignItems?'align-items:'+s.alignItems+';':''}${s.flexGap?'gap:'+s.flexGap+'px;':''}${s.flexWrap?'flex-wrap:wrap;':''}` : '';
      const gridStyle    = s.display==='grid' ? `${s.gridCols?'grid-template-columns:'+s.gridCols+';':''}${s.gridRows?'grid-template-rows:'+s.gridRows+';':''}${s.gridGap?'gap:'+s.gridGap+'px;':''}` : '';
      const typoStyle    = `${s.fontWeight?'font-weight:'+s.fontWeight+';':''}${s.textColor?'color:'+s.textColor+';':''}${s.lineHeight&&s.lineHeight!==1.5?'line-height:'+s.lineHeight+';':''}${s.letterSpacing?'letter-spacing:'+s.letterSpacing+'em;':''}${s.textTransform&&s.textTransform!=='none'?'text-transform:'+s.textTransform+';':''}`;
      const hoverStyle   = (s.hoverBg||s.hoverColor||s.hoverShadow) ? `--hover-bg:${s.hoverBg||''};--hover-color:${s.hoverColor||''};transition:all ${s.transition||0.3}s ease;` : '';
      const transformStyle = `${s.opacity!==undefined&&s.opacity!==1?'opacity:'+s.opacity+';':''}${(s.rotate||s.scale)?`transform:${s.rotate?'rotate('+s.rotate+'deg)':''} ${s.scale&&s.scale!==1?'scale('+s.scale+')':''};`:''}`;

      const wrapStyle = `
        padding:${pt}px ${pr}px ${pb}px ${pl}px;
        margin:${mt}px ${mr}px ${mb}px ${ml}px;
        background:${bg};
        border-radius:${s.borderRadius||0}px;
        ${borderStyle}${shadowStyle}${widthStyle}${heightStyle}
        ${displayStyle}${flexStyle}${gridStyle}
        ${typoStyle}${hoverStyle}${transformStyle}
        ${s.customCss||''}
      `;
      const id  = s.elementId ? `id="${s.elementId}"` : '';
      const cls = s.cssClasses || '';

      const renders = {
        section: () => `<div ${id} class="w-full ${cls}" style="${wrapStyle} background-image:${s.bgImage?`url(${s.bgImage})`:'none'};background-size:${s.bgSize||'cover'};min-height:100px;">
          <div class="mx-auto max-w-6xl px-6">${this.renderChildren(comp)}</div>
        </div>`,

        container: () => `<div ${id} class="mx-auto px-6 ${cls}" style="${wrapStyle} max-width:${s.maxWidth||'1200px'}">
          ${this.renderChildren(comp)}
        </div>`,

        columns: () => {
          const count = s.columnCount || 2;
          const cols  = s.columns || Array(count).fill([]);
          return `<div ${id} class="flex gap-4 flex-wrap ${cls}" style="${wrapStyle}">
            ${cols.slice(0,count).map((col,i) => `<div class="col-dropzone flex-1 min-w-0 p-2" data-col="${comp.id}-${i}">
              ${col.length ? col.map(c => this.renderWidget(c)).join('') : `<p class="text-xs text-gray-300 text-center py-4">Drop here</p>`}
            </div>`).join('')}
          </div>`;
        },

        spacer: () => `<div ${id} class="${cls}" style="height:${s.height||40}px;${wrapStyle}"></div>`,

        divider: () => `<div ${id} class="flex justify-${s.alignment==='center'?'center':s.alignment==='right'?'end':'start'} ${cls}" style="${wrapStyle}">
          <hr style="border-top:${s.thickness||1}px ${s.style||'solid'} ${s.color||'#e2e8f0'};width:${s.width||100}%;margin:0">
        </div>`,

        heading: () => {
          const tag = s.tag || 'h2';
          return `<${tag} ${id} class="w-full ${cls}" style="text-align:${s.alignment||'left'};color:${s.color||'#1e293b'};font-size:${s.fontSize||36}px;font-weight:${s.fontWeight||700};${wrapStyle}">${s.text||'Heading'}</${tag}>`;
        },

        paragraph: () => `<div ${id} class="${cls}" style="text-align:${s.alignment||'left'};${wrapStyle}">${s.content||'<p>Paragraph text</p>'}</div>`,

        button: () => `<div class="flex justify-${s.alignment==='center'?'center':s.alignment==='right'?'end':'start'} ${cls}" style="${wrapStyle}">
          <a href="${this.livePreview?s.link:'#'}" style="background-color:${s.bgColor||'#0ea5e9'};color:${s.textColor||'#fff'};border-radius:${s.borderRadius||8}px;padding:${s.size==='sm'?'8px 16px':s.size==='lg'?'14px 32px':'10px 24px'};font-size:${s.size==='sm'?'14px':s.size==='lg'?'18px':'16px'};font-weight:600;text-decoration:none;display:inline-block;">${s.text||'Button'}</a>
        </div>`,

        image: () => `<div class="flex justify-${s.alignment==='center'?'center':s.alignment==='right'?'end':'start'} ${cls}" style="${wrapStyle}">
          <img src="${s.url||'https://picsum.photos/800/400'}" alt="${s.alt||''}" style="width:${s.width||100}%;max-width:100%;border-radius:${s.borderRadius||0}px;">
        </div>`,

        video: () => `<div class="${cls}" style="${wrapStyle}">
          <div style="position:relative;padding-bottom:${s.ratio==='4/3'?'75%':s.ratio==='1/1'?'100%':'56.25%'};height:0;overflow:hidden;border-radius:${s.borderRadius||0}px;">
            <iframe src="${s.url||''}" style="position:absolute;top:0;left:0;width:100%;height:100%;border:0;" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
          </div>
        </div>`,

        icon: () => `<div class="flex justify-${s.alignment==='center'?'center':s.alignment==='right'?'end':'start'} ${cls}" style="${wrapStyle}">
          <i class="${s.iconClass||'fas fa-star'}" style="font-size:${s.size||40}px;color:${s.color||'#0ea5e9'}"></i>
        </div>`,

        'icon-list': () => `<ul class="${cls}" style="${wrapStyle};list-style:none;padding:0;margin:0;text-align:${s.alignment||'left'}">
          ${(s.items||[]).map(item => `<li style="display:flex;align-items:center;gap:8px;margin-bottom:8px;${s.alignment==='center'?'justify-content:center':''}">
            <i class="${item.icon||'fas fa-check'}" style="color:${s.iconColor||'#0ea5e9'};font-size:${s.iconSize||16}px;"></i>
            <span>${item.text||'List item'}</span>
          </li>`).join('')}
        </ul>`,

        testimonial: () => `<div ${id} class="${cls}" style="${wrapStyle};background:#f8fafc;border-radius:12px;padding:24px;">
          <div style="display:flex;gap:4px;margin-bottom:12px;">${Array(s.rating||5).fill('').map(()=>'<i class="fas fa-star" style="color:#f59e0b;font-size:16px;"></i>').join('')}</div>
          <p style="font-style:italic;margin:0 0 16px;color:#475569;">"${s.text||'Testimonial text'}"</p>
          <div style="display:flex;align-items:center;gap:12px;">
            <img src="${s.photo||'https://i.pravatar.cc/80'}" style="width:48px;height:48px;border-radius:50%;object-fit:cover;">
            <div>
              <p style="font-weight:700;margin:0;">${s.author||'Author'}</p>
              <p style="color:#64748b;font-size:14px;margin:0;">${s.role||'Role'}</p>
            </div>
          </div>
        </div>`,

        'team-member': () => `<div ${id} class="text-center ${cls}" style="${wrapStyle};background:#fff;border-radius:12px;padding:24px;box-shadow:0 1px 8px rgba(0,0,0,0.08);">
          <img src="${s.photo||'https://i.pravatar.cc/200'}" style="width:100px;height:100px;border-radius:50%;object-fit:cover;margin:0 auto 16px;">
          <h4 style="font-weight:700;font-size:18px;margin:0 0 4px;">${s.name||'Name'}</h4>
          <p style="color:#64748b;font-size:14px;margin:0 0 12px;">${s.role||'Role'}</p>
          <p style="color:#475569;font-size:14px;">${s.bio||'Bio'}</p>
          <div style="display:flex;justify-content:center;gap:12px;margin-top:16px;">
            ${s.social&&s.social.twitter?`<a href="${s.social.twitter}" style="color:#1da1f2;"><i class="fab fa-twitter"></i></a>`:''}
            ${s.social&&s.social.linkedin?`<a href="${s.social.linkedin}" style="color:#0077b5;"><i class="fab fa-linkedin"></i></a>`:''}
            ${s.social&&s.social.github?`<a href="${s.social.github}" style="color:#333;"><i class="fab fa-github"></i></a>`:''}
          </div>
        </div>`,

        pricing: () => `<div ${id} class="${cls}" style="${wrapStyle};background:${s.highlighted?this.globalStyles.primaryColor:'#fff'};color:${s.highlighted?'#fff':'inherit'};border-radius:16px;padding:32px 24px;box-shadow:0 4px 20px rgba(0,0,0,0.1);text-align:center;${s.highlighted?'transform:scale(1.05)':''}">
          <h3 style="font-weight:700;font-size:20px;margin:0 0 8px;">${s.title||'Plan'}</h3>
          <div style="font-size:48px;font-weight:900;margin:16px 0;">${s.currency||'$'}${s.price||'0'}<span style="font-size:16px;font-weight:400;">${s.period||'/mo'}</span></div>
          <ul style="list-style:none;padding:0;margin:0 0 24px;text-align:left;">
            ${(s.features||[]).map(f=>`<li style="padding:6px 0;display:flex;align-items:center;gap:8px;"><i class="fas fa-check-circle" style="color:${s.highlighted?'#fff':'#22c55e'}"></i>${f}</li>`).join('')}
          </ul>
          <button style="width:100%;padding:12px;background:${s.highlighted?'rgba(255,255,255,0.2)':'#0ea5e9'};color:#fff;border:none;border-radius:8px;font-size:16px;font-weight:600;cursor:pointer;">${s.buttonText||'Get Started'}</button>
        </div>`,

        accordion: () => `<div ${id} class="${cls}" style="${wrapStyle}">
          ${(s.items||[]).map((item) => `<div style="border:1px solid #e2e8f0;border-radius:8px;margin-bottom:8px;overflow:hidden;">
            <button onclick="this.nextElementSibling.style.display=this.nextElementSibling.style.display==='none'?'block':'none'" style="width:100%;text-align:left;padding:12px 16px;background:#f8fafc;border:none;font-weight:600;cursor:pointer;display:flex;justify-content:space-between;align-items:center;">
              ${item.title}<i class="fas fa-chevron-down"></i>
            </button>
            <div style="display:${item.open?'block':'none'};padding:16px;">${item.content||''}</div>
          </div>`).join('')}
        </div>`,

        tabs: () => `<div ${id} class="${cls}" style="${wrapStyle}">
          <div style="display:flex;border-bottom:2px solid #e2e8f0;margin-bottom:16px;">
            ${(s.items||[]).map((tab,i) => `<button onclick="this.closest('[data-tabs]').querySelectorAll('[data-tab-content]').forEach((c,ci)=>c.style.display=ci===parseInt(this.dataset.idx)?'block':'none');this.closest('[data-tabs]').querySelectorAll('[data-tab-btn]').forEach(b=>b.style.borderBottom=b===this?'2px solid #0ea5e9':'none')" data-tab-btn data-idx="${i}" style="padding:8px 16px;border:none;background:none;cursor:pointer;font-weight:600;color:${i===0?'#0ea5e9':'#64748b'};border-bottom:${i===0?'2px solid #0ea5e9':'none'};margin-bottom:-2px;">${tab.label||'Tab '+i}</button>`).join('')}
          </div>
          <div data-tabs>
            ${(s.items||[]).map((tab,i) => `<div data-tab-content style="display:${i===0?'block':'none'};">${tab.content||''}</div>`).join('')}
          </div>
        </div>`,

        counter: () => `<div ${id} class="text-center ${cls}" style="${wrapStyle}">
          <div style="font-size:${s.fontSize||48}px;font-weight:900;color:${s.color||'#0ea5e9'};">${s.prefix||''}${s.end||100}${s.suffix||'+'}</div>
          <p style="margin:4px 0 0;color:#64748b;">${s.label||'Counter'}</p>
        </div>`,

        'progress-bar': () => `<div ${id} class="${cls}" style="${wrapStyle}">
          <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
            <span style="font-weight:600;">${s.label||'Skill'}</span>
            <span style="color:#64748b;">${s.percentage||75}%</span>
          </div>
          <div style="background:#e2e8f0;border-radius:${s.height||12}px;height:${s.height||12}px;overflow:hidden;">
            <div style="width:${s.percentage||75}%;height:100%;background:${s.color||'#0ea5e9'};border-radius:${s.height||12}px;${s.striped?'background-image:linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-size:1rem 1rem;':''}transition:width 0.6s ease;"></div>
          </div>
        </div>`,

        'circle-progress': () => {
          const r    = (s.size||120)/2 - (s.strokeWidth||10);
          const circ = 2*Math.PI*r;
          const dash = circ * (s.percentage||75)/100;
          return `<div ${id} class="flex flex-col items-center ${cls}" style="${wrapStyle}">
            <svg width="${s.size||120}" height="${s.size||120}" viewBox="0 0 ${s.size||120} ${s.size||120}" style="transform:rotate(-90deg)">
              <circle cx="${(s.size||120)/2}" cy="${(s.size||120)/2}" r="${r}" fill="none" stroke="#e2e8f0" stroke-width="${s.strokeWidth||10}"/>
              <circle cx="${(s.size||120)/2}" cy="${(s.size||120)/2}" r="${r}" fill="none" stroke="${s.color||'#0ea5e9'}" stroke-width="${s.strokeWidth||10}" stroke-dasharray="${dash} ${circ}" stroke-linecap="round"/>
            </svg>
            <p style="font-weight:700;font-size:18px;margin:8px 0 0;">${s.label||s.percentage+'%'}</p>
          </div>`;
        },

        countdown: () => {
          const target = new Date(s.targetDate||Date.now()).getTime();
          const diff   = Math.max(0, target - Date.now());
          const d      = Math.floor(diff/86400000);
          const h      = Math.floor((diff%86400000)/3600000);
          const m      = Math.floor((diff%3600000)/60000);
          const sec    = Math.floor((diff%60000)/1000);
          return `<div ${id} class="flex gap-4 justify-center flex-wrap ${cls}" style="${wrapStyle}">
            ${[{v:d,l:s.labelsDay||'Days'},{v:h,l:s.labelsHour||'Hours'},{v:m,l:s.labelsMin||'Min'},{v:sec,l:s.labelsSec||'Sec'}].map(x=>`
            <div style="text-align:center;background:#f8fafc;border-radius:12px;padding:16px 24px;">
              <div style="font-size:40px;font-weight:900;color:${s.color||'#0ea5e9'};line-height:1;">${String(x.v).padStart(2,'0')}</div>
              <p style="margin:4px 0 0;font-size:12px;color:#64748b;text-transform:uppercase;">${x.l}</p>
            </div>`).join('')}
          </div>`;
        },

        'image-carousel': () => `<div ${id} class="${cls}" style="${wrapStyle};position:relative;overflow:hidden;border-radius:${s.borderRadius||0}px;">
          <div style="display:flex;overflow:hidden;">
            ${(s.images||[]).map((img,i)=>`<div style="min-width:100%;${i===0?'':'display:none'}">
              <img src="${img}" style="width:100%;display:block;">
              ${s.captions&&s.captions[i]?`<div style="position:absolute;bottom:0;left:0;right:0;background:rgba(0,0,0,0.5);color:#fff;padding:12px;text-align:center;">${s.captions[i]}</div>`:''}
            </div>`).join('')}
          </div>
          <div style="position:absolute;bottom:12px;left:50%;transform:translateX(-50%);display:flex;gap:6px;">
            ${(s.images||[]).map((_,i)=>`<div style="width:8px;height:8px;border-radius:50%;background:${i===0?'#fff':'rgba(255,255,255,0.5)'}"></div>`).join('')}
          </div>
          <button onclick="this.closest('[style]').querySelector('div>div').scrollBy(-400,0)" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);background:rgba(0,0,0,0.5);color:#fff;border:none;border-radius:50%;width:36px;height:36px;cursor:pointer;font-size:16px;">‹</button>
          <button onclick="this.closest('[style]').querySelector('div>div').scrollBy(400,0)" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:rgba(0,0,0,0.5);color:#fff;border:none;border-radius:50%;width:36px;height:36px;cursor:pointer;font-size:16px;">›</button>
        </div>`,

        'before-after': () => `<div ${id} class="${cls}" style="${wrapStyle};position:relative;overflow:hidden;user-select:none;" onmousedown="this.dataset.drag='1'" onmouseup="delete this.dataset.drag" onmousemove="if(this.dataset.drag){const r=this.getBoundingClientRect();const pct=((event.clientX-r.left)/r.width*100).toFixed(1);this.querySelector('.ba-after').style.clipPath='inset(0 0 0 '+pct+'%)';this.querySelector('.ba-handle').style.left=pct+'%'}">
          <img src="${s.beforeUrl||'https://picsum.photos/800/400?random=14'}" style="width:100%;display:block;">
          <div class="ba-after" style="position:absolute;top:0;left:0;right:0;bottom:0;clip-path:inset(0 0 0 50%);">
            <img src="${s.afterUrl||'https://picsum.photos/800/400?random=15'}" style="width:100%;display:block;">
          </div>
          <div class="ba-handle" style="position:absolute;top:0;bottom:0;left:50%;width:4px;background:white;cursor:ew-resize;transform:translateX(-50%);">
            <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:32px;height:32px;background:white;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,0.3);font-size:16px;">⟺</div>
          </div>
          <div style="position:absolute;top:12px;left:12px;background:rgba(0,0,0,0.6);color:#fff;padding:4px 10px;border-radius:4px;font-size:12px;">Before</div>
          <div style="position:absolute;top:12px;right:12px;background:rgba(0,0,0,0.6);color:#fff;padding:4px 10px;border-radius:4px;font-size:12px;">After</div>
        </div>`,

        lottie: () => `<div ${id} class="${cls}" style="${wrapStyle};height:${s.height||300}px;display:flex;align-items:center;justify-content:center;background:#f8fafc;border-radius:8px;">
          ${s.url ? `<div style="width:100%;height:100%;">Lottie: ${s.url}</div>` : '<p style="color:#94a3b8;">Add a Lottie JSON URL in settings</p>'}
        </div>`,

        'google-maps': () => `<div ${id} class="${cls}" style="${wrapStyle};border-radius:${s.borderRadius||0}px;overflow:hidden;">
          <iframe width="100%" height="${s.height||400}" frameborder="0" style="border:0" src="https://maps.google.com/maps?q=${encodeURIComponent(s.address||'New York')}&output=embed&hl=en" allowfullscreen></iframe>
        </div>`,

        'post-loop': () => {
          const posts = [
            {title:'Getting Started with AI',   date:'May 1, 2026',  cat:'Technology',  excerpt:'Learn the fundamentals...',    img:'https://picsum.photos/400/250?random=20'},
            {title:'Design Trends 2026',         date:'Apr 28, 2026', cat:'Design',       excerpt:'Explore the latest...',        img:'https://picsum.photos/400/250?random=21'},
            {title:'Remote Work Tips',           date:'Apr 25, 2026', cat:'Productivity', excerpt:'Boost your productivity...',   img:'https://picsum.photos/400/250?random=22'},
            {title:'Web Performance Guide',      date:'Apr 22, 2026', cat:'Development',  excerpt:'Speed matters more...',        img:'https://picsum.photos/400/250?random=23'},
            {title:'UX Research Methods',        date:'Apr 20, 2026', cat:'UX',           excerpt:'Understanding users...',       img:'https://picsum.photos/400/250?random=24'},
            {title:'Marketing Automation',       date:'Apr 18, 2026', cat:'Marketing',    excerpt:'Save time with automation...', img:'https://picsum.photos/400/250?random=25'},
          ].slice(0, s.count||6);
          const cols = s.columns||3;
          return `<div ${id} class="${cls}" style="${wrapStyle}">
            <div style="display:grid;grid-template-columns:repeat(${cols},1fr);gap:24px;">
              ${posts.map(p=>`<article style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 1px 8px rgba(0,0,0,0.08);">
                <img src="${p.img}" style="width:100%;height:180px;object-fit:cover;">
                <div style="padding:16px;">
                  <span style="font-size:11px;color:#0ea5e9;font-weight:600;text-transform:uppercase;">${p.cat}</span>
                  <h4 style="font-size:16px;font-weight:700;margin:8px 0;">${p.title}</h4>
                  <p style="font-size:13px;color:#64748b;margin:0 0 12px;">${p.excerpt}</p>
                  <div style="display:flex;align-items:center;justify-content:space-between;">
                    <span style="font-size:12px;color:#94a3b8;">${p.date}</span>
                    <a href="#" style="font-size:12px;color:#0ea5e9;text-decoration:none;font-weight:600;">Read more →</a>
                  </div>
                </div>
              </article>`).join('')}
            </div>
          </div>`;
        },

        'post-meta': () => `<div ${id} class="flex flex-wrap gap-4 ${cls}" style="${wrapStyle};font-size:14px;color:#64748b;">
          ${s.author  ?`<span><i class="fas fa-user"     style="margin-right:4px;"></i> John Smith</span>`  :''}
          ${s.date    ?`<span><i class="fas fa-calendar" style="margin-right:4px;"></i> May 1, 2026</span>`  :''}
          ${s.category?`<span><i class="fas fa-folder"   style="margin-right:4px;"></i> Technology</span>`  :''}
          ${s.comments?`<span><i class="fas fa-comment"  style="margin-right:4px;"></i> 12 Comments</span>` :''}
        </div>`,

        'author-box': () => `<div ${id} class="${cls}" style="${wrapStyle};background:#f8fafc;border-radius:12px;padding:24px;display:flex;gap:20px;align-items:flex-start;">
          <img src="${s.photo||'https://i.pravatar.cc/100'}" style="width:80px;height:80px;border-radius:50%;object-fit:cover;flex-shrink:0;">
          <div>
            <h4 style="font-weight:700;margin:0 0 4px;">${s.name||'Author Name'}</h4>
            <p style="color:#64748b;font-size:14px;margin:0;">${s.bio||'Author bio'}</p>
          </div>
        </div>`,

        'custom-field': () => `<div ${id} class="${cls}" style="${wrapStyle};padding:12px;background:#f8fafc;border-radius:8px;display:flex;gap:8px;">
          <span style="font-weight:600;color:#64748b;">${s.fieldKey||'Key'}:</span>
          <span>${s.fieldValue||'Value'}</span>
        </div>`,

        'contact-form': () => `<div ${id} class="${cls}" style="${wrapStyle};background:#f8fafc;border-radius:12px;padding:32px;">
          <h3 style="font-weight:700;font-size:24px;margin:0 0 24px;">${s.title||'Contact Us'}</h3>
          <div style="display:flex;flex-direction:column;gap:16px;">
            <input type="text"  placeholder="Your Name"      style="width:100%;padding:12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;box-sizing:border-box;">
            <input type="email" placeholder="Email Address"  style="width:100%;padding:12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;box-sizing:border-box;">
            <textarea placeholder="Your Message" rows="4"    style="width:100%;padding:12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;box-sizing:border-box;resize:none;"></textarea>
            <button onclick="this.closest('div').querySelector('.form-success').style.display='block';this.style.display='none'" style="padding:12px 24px;background:#0ea5e9;color:#fff;border:none;border-radius:8px;font-size:16px;font-weight:600;cursor:pointer;">${s.submitText||'Send Message'}</button>
            <div class="form-success" style="display:none;padding:12px;background:#dcfce7;color:#16a34a;border-radius:8px;font-weight:600;">${s.successMsg||'Thank you!'}</div>
          </div>
        </div>`,

        'subscribe-form': () => `<div ${id} class="${cls}" style="${wrapStyle};display:flex;gap:8px;max-width:480px;">
          <input type="email" placeholder="${s.placeholder||'Enter your email'}" style="flex:1;padding:12px 16px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;">
          <button style="padding:12px 24px;background:#0ea5e9;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;white-space:nowrap;">${s.buttonText||'Subscribe'}</button>
        </div>`,

        'search-form': () => `<div ${id} class="${cls}" style="${wrapStyle};display:flex;gap:0;max-width:480px;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;">
          <input type="text" placeholder="${s.placeholder||'Search...'}" style="flex:1;padding:12px 16px;border:none;font-size:14px;outline:none;">
          <button style="padding:12px 20px;background:#0ea5e9;color:#fff;border:none;font-size:14px;font-weight:600;cursor:pointer;">${s.buttonText||'Search'}</button>
        </div>`,

        'raw-html': () => `<div ${id} class="${cls}" style="${wrapStyle}">${s.code||''}</div>`,

        'alert-box': () => {
          const colors = {
            info:    {bg:'#eff6ff',border:'#bfdbfe',text:'#1d4ed8',icon:'fa-info-circle'},
            success: {bg:'#f0fdf4',border:'#bbf7d0',text:'#15803d',icon:'fa-check-circle'},
            warning: {bg:'#fffbeb',border:'#fde68a',text:'#b45309',icon:'fa-exclamation-triangle'},
            error:   {bg:'#fef2f2',border:'#fecaca',text:'#dc2626',icon:'fa-times-circle'},
          };
          const t = s.type||'info'; const c = colors[t]||colors.info;
          return `<div ${id} class="${cls}" style="${wrapStyle};background:${c.bg};border:1px solid ${c.border};border-radius:8px;padding:12px 16px;display:flex;align-items:flex-start;gap:12px;">
            ${s.icon!==false?`<i class="fas ${c.icon}" style="color:${c.text};margin-top:2px;flex-shrink:0;"></i>`:''}
            <div style="flex:1;">
              ${s.title?`<p style="font-weight:700;color:${c.text};margin:0 0 4px;">${s.title}</p>`:''}
              <p style="color:${c.text};margin:0;font-size:14px;">${s.message||'Alert message here'}</p>
            </div>
            ${s.dismissible?`<button onclick="this.closest('[style]').remove()" style="background:none;border:none;cursor:pointer;color:${c.text};opacity:0.6;font-size:16px;line-height:1;padding:0;">×</button>`:''}
          </div>`;
        },

        'breadcrumbs': () => `<nav ${id} class="${cls}" style="${wrapStyle}" aria-label="Breadcrumb">
          <ol style="display:flex;align-items:center;gap:4px;list-style:none;padding:0;margin:0;font-size:14px;flex-wrap:wrap;">
            ${(s.items||[]).map((item,i)=>`<li style="display:flex;align-items:center;gap:4px;">
              ${i>0?`<span style="color:#94a3b8;margin-right:4px;">${s.separator||'/'}</span>`:''}
              ${item.link && i<(s.items.length-1) ? `<a href="${item.link}" style="color:#0ea5e9;text-decoration:none;">${item.label}</a>` : `<span style="${i===(s.items.length-1)?'color:#64748b;font-weight:600':''}">${item.label}</span>`}
            </li>`).join('')}
          </ol>
        </nav>`,

        'table': () => `<div ${id} class="${cls}" style="${wrapStyle};overflow-x:auto;">
          <table style="width:100%;border-collapse:collapse;font-size:14px;${s.bordered?'border:1px solid #e2e8f0;':''}">
            <thead>
              <tr style="background:#f1f5f9;">
                ${(s.headers||[]).map(h=>`<th style="padding:10px 14px;text-align:left;font-weight:700;color:#475569;${s.bordered?'border:1px solid #e2e8f0;':''}">${h}</th>`).join('')}
              </tr>
            </thead>
            <tbody>
              ${(s.rows||[]).map((row,ri)=>`<tr style="${s.striped&&ri%2===1?'background:#f8fafc;':''}">
                ${row.map(cell=>`<td style="padding:10px 14px;${s.bordered?'border:1px solid #e2e8f0;':'border-bottom:1px solid #f1f5f9;'}color:#374151;">${cell}</td>`).join('')}
              </tr>`).join('')}
            </tbody>
          </table>
        </div>`,

        'modal-trigger': () => {
          const mid = s.modalId || ('m_'+comp.id.slice(-6));
          return `<div ${id} class="${cls}" style="${wrapStyle}">
            <button onclick="document.getElementById('modal_${mid}').style.display='flex'" style="padding:10px 24px;background:${s.triggerBg||'#0ea5e9'};color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;">${s.triggerText||'Open Modal'}</button>
            <div id="modal_${mid}" style="display:none;position:fixed;inset:0;z-index:9000;align-items:center;justify-content:center;background:rgba(0,0,0,0.5);" onclick="if(event.target===this)this.style.display='none'">
              <div style="background:#fff;border-radius:16px;padding:32px;max-width:500px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,0.2);position:relative;">
                <button onclick="document.getElementById('modal_${mid}').style.display='none'" style="position:absolute;top:12px;right:16px;background:none;border:none;font-size:22px;cursor:pointer;color:#94a3b8;">×</button>
                <h3 style="font-weight:700;font-size:20px;margin:0 0 16px;">${s.modalTitle||'Modal Title'}</h3>
                <div>${s.modalContent||'<p>Modal content here.</p>'}</div>
              </div>
            </div>
          </div>`;
        },

        'form-advanced': () => {
          const fid = 'f_'+comp.id.slice(-6);
          return `<div ${id} class="${cls}" style="${wrapStyle};background:#f8fafc;border-radius:12px;padding:32px;">
            ${s.title?`<h3 style="font-weight:700;font-size:22px;margin:0 0 20px;">${s.title}</h3>`:''}
            <div id="${fid}_success" style="display:none;padding:12px;background:#dcfce7;color:#16a34a;border-radius:8px;margin-bottom:16px;font-weight:600;">${s.successMsg||'Submitted!'}</div>
            <form onsubmit="event.preventDefault();document.getElementById('${fid}_success').style.display='block';this.style.display='none'" style="display:flex;flex-direction:column;gap:14px;">
              ${(s.fields||[]).map(f=>{
                if (f.type==='checkbox') return `<label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:14px;"><input type="checkbox"> ${f.label}</label>`;
                if (f.type==='radio')    return `<div><p style="font-size:13px;font-weight:600;color:#64748b;margin:0 0 6px;">${f.label}</p>${(f.options||[]).map(o=>`<label style="display:flex;align-items:center;gap:6px;font-size:14px;margin-bottom:4px;cursor:pointer;"><input type="radio" name="${fid}_${f.label}"> ${o}</label>`).join('')}</div>`;
                if (f.type==='select')   return `<div><label style="font-size:13px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">${f.label}${f.required?'*':''}</label><select style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;">${(f.options||[]).map(o=>`<option>${o}</option>`).join('')}</select></div>`;
                if (f.type==='textarea') return `<div><label style="font-size:13px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">${f.label}${f.required?'*':''}</label><textarea rows="4" style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;resize:none;box-sizing:border-box;" placeholder="${f.label}..."></textarea></div>`;
                if (f.type==='file')     return `<div><label style="font-size:13px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">${f.label}</label><input type="file" style="width:100%;font-size:14px;"></div>`;
                return `<div><label style="font-size:13px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">${f.label}${f.required?'*':''}</label><input type="${f.type||'text'}" placeholder="${f.label}..." ${f.required?'required':''} style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;box-sizing:border-box;"></div>`;
              }).join('')}
              <button type="submit" style="padding:12px;background:#0ea5e9;color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;">${s.submitText||'Submit'}</button>
            </form>
          </div>`;
        },
      }; // ── end original widgets ──────────────────────────────────────────

      // ================================================================
      // PRE-BUILT SECTION WIDGETS (Headers, Heroes, Features, Footers…)
      // ================================================================

      // ── ANNOUNCEMENT BAR ──────────────────────────────────────────────
      renders['announcement-bar'] = () => `<div ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#0ea5e9'};color:${s.textColor||'#fff'};text-align:center;padding:10px 20px;font-size:14px;font-weight:500;">
        ${s.text||'🎉 Special offer — limited time only!'}
        ${s.ctaText?`<a href="${s.ctaLink||'#'}" style="margin-left:12px;padding:3px 12px;background:rgba(255,255,255,0.2);border-radius:4px;color:inherit;text-decoration:none;font-weight:700;">${s.ctaText} →</a>`:''}
      </div>`;

      // ── NAV SIMPLE ────────────────────────────────────────────────────
      renders['nav-simple'] = () => {
        const links = (s.links||'Home,About,Services,Contact').split(',');
        return `<nav ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#fff'};box-shadow:0 1px 12px rgba(0,0,0,0.07);padding:16px 40px;display:flex;align-items:center;justify-content:space-between;">
          <div style="font-weight:800;font-size:20px;color:#0ea5e9;">${s.logo||'MySite'}</div>
          <div style="display:flex;gap:28px;align-items:center;">
            ${links.map(l=>`<a href="#" style="color:${s.textColor||'#475569'};text-decoration:none;font-size:15px;font-weight:500;transition:color 0.15s;" onmouseover="this.style.color='#0ea5e9'" onmouseout="this.style.color='${s.textColor||'#475569'}'">${l.trim()}</a>`).join('')}
            ${s.ctaText?`<a href="${s.ctaLink||'#'}" style="background:#0ea5e9;color:#fff;padding:9px 22px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:700;">${s.ctaText}</a>`:''}
          </div>
        </nav>`;
      };

      // ── NAV SAAS ──────────────────────────────────────────────────────
      renders['nav-saas'] = () => {
        const links = (s.links||'Features,Pricing,Docs,Blog').split(',');
        return `<nav ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#fff'};border-bottom:1px solid #f1f5f9;padding:14px 40px;display:flex;align-items:center;justify-content:space-between;">
          <div style="display:flex;align-items:center;gap:36px;">
            <div style="font-weight:800;font-size:18px;color:#0ea5e9;">${s.logo||'SaaSApp'}</div>
            <div style="display:flex;gap:24px;">
              ${links.map(l=>`<a href="#" style="color:#475569;text-decoration:none;font-size:14px;font-weight:500;">${l.trim()}</a>`).join('')}
            </div>
          </div>
          <div style="display:flex;align-items:center;gap:12px;">
            <a href="#" style="color:#475569;text-decoration:none;font-size:14px;font-weight:500;">${s.loginText||'Log In'}</a>
            <a href="#" style="background:#0ea5e9;color:#fff;padding:8px 20px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:700;">${s.ctaText||'Start Free Trial'}</a>
          </div>
        </nav>`;
      };

      // ── NAV CENTERED ─────────────────────────────────────────────────
      renders['nav-centered'] = () => {
        const links = (s.links||'Home,About,Portfolio,Blog,Contact').split(',');
        return `<nav ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#fff'};box-shadow:0 1px 8px rgba(0,0,0,0.06);padding:12px 40px;text-align:center;">
          <div style="font-weight:900;font-size:22px;color:#0f172a;margin-bottom:10px;">${s.logo||'MySite'}</div>
          <div style="display:flex;gap:28px;justify-content:center;">
            ${links.map(l=>`<a href="#" style="color:#475569;text-decoration:none;font-size:14px;font-weight:500;">${l.trim()}</a>`).join('')}
          </div>
        </nav>`;
      };

      // ── NAV ECOMMERCE ────────────────────────────────────────────────
      renders['nav-ecommerce'] = () => {
        const links = (s.links||'Home,Shop,Sale,About').split(',');
        return `<nav ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#fff'};box-shadow:0 1px 8px rgba(0,0,0,0.06);padding:14px 40px;display:flex;align-items:center;justify-content:space-between;">
          <div style="font-weight:800;font-size:20px;">${s.logo||'MyShop'}</div>
          <div style="display:flex;gap:24px;">
            ${links.map(l=>`<a href="#" style="color:#374151;text-decoration:none;font-size:14px;font-weight:500;">${l.trim()}</a>`).join('')}
          </div>
          <div style="display:flex;align-items:center;gap:16px;">
            <i class="fas fa-search" style="color:#64748b;cursor:pointer;"></i>
            <i class="fas fa-heart"  style="color:#64748b;cursor:pointer;"></i>
            <div style="position:relative;cursor:pointer;">
              <i class="fas fa-shopping-bag" style="font-size:18px;color:#374151;"></i>
              <span style="position:absolute;top:-8px;right:-8px;background:#ef4444;color:#fff;border-radius:50%;width:18px;height:18px;font-size:10px;font-weight:700;display:flex;align-items:center;justify-content:center;">${s.cartCount||0}</span>
            </div>
          </div>
        </nav>`;
      };

      // ── HERO SPLIT ───────────────────────────────────────────────────
      renders['hero-split'] = () => `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#f8fafc'};padding:${s.pt||80}px 40px ${s.pb||80}px;">
        <div style="max-width:1200px;margin:0 auto;display:flex;align-items:center;gap:60px;flex-wrap:wrap;">
          <div style="flex:1;min-width:280px;">
            ${s.badge?`<span style="display:inline-block;background:#eff6ff;color:#0ea5e9;padding:5px 14px;border-radius:20px;font-size:12px;font-weight:700;margin-bottom:18px;">${s.badge}</span>`:''}
            <h1 style="font-size:52px;font-weight:900;line-height:1.12;color:#0f172a;margin:0 0 20px;">${(s.headline||'Build Faster,<br>Launch Smarter').replace(/\\n/g,'<br>')}</h1>
            <p style="font-size:18px;color:#64748b;line-height:1.7;margin:0 0 32px;">${s.subtext||'The all-in-one platform to ship faster.'}</p>
            <div style="display:flex;gap:12px;flex-wrap:wrap;">
              <a href="${s.ctaLink||'#'}" style="background:#0ea5e9;color:#fff;padding:14px 30px;border-radius:10px;text-decoration:none;font-size:16px;font-weight:700;box-shadow:0 4px 14px rgba(14,165,233,0.3);">${s.ctaText||'Get Started'}</a>
              ${s.ctaSecondary?`<a href="#" style="color:#374151;padding:14px 28px;border-radius:10px;text-decoration:none;font-size:16px;font-weight:600;border:2px solid #e2e8f0;">${s.ctaSecondary}</a>`:''}
            </div>
          </div>
          <div style="flex:1;min-width:280px;">
            <img src="${s.imageUrl||'https://picsum.photos/600/500?random=101'}" style="width:100%;border-radius:16px;box-shadow:0 20px 60px rgba(0,0,0,0.12);">
          </div>
        </div>
      </section>`;

      // ── HERO CENTERED ─────────────────────────────────────────────────
      renders['hero-centered'] = () => `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#ffffff'};padding:${s.pt||100}px 40px ${s.pb||100}px;text-align:center;">
        <div style="max-width:800px;margin:0 auto;">
          ${s.badge?`<span style="display:inline-block;background:#eff6ff;color:#0ea5e9;padding:6px 16px;border-radius:20px;font-size:13px;font-weight:700;margin-bottom:20px;">${s.badge}</span>`:''}
          <h1 style="font-size:56px;font-weight:900;line-height:1.1;color:#0f172a;margin:0 0 20px;">${(s.headline||'The Future of Web Design Is Here').replace(/\\n/g,'<br>')}</h1>
          <p style="font-size:19px;color:#64748b;line-height:1.7;margin:0 0 36px;">${s.subtext||'Create stunning websites without writing a single line of code.'}</p>
          <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
            <a href="${s.ctaLink||'#'}" style="background:#0ea5e9;color:#fff;padding:16px 34px;border-radius:10px;text-decoration:none;font-size:16px;font-weight:700;box-shadow:0 4px 14px rgba(14,165,233,0.35);">${s.ctaText||'Get Started Free'}</a>
            ${s.ctaSecondary?`<a href="#" style="color:#374151;padding:16px 28px;border-radius:10px;text-decoration:none;font-size:16px;font-weight:600;border:2px solid #e2e8f0;">${s.ctaSecondary}</a>`:''}
          </div>
        </div>
      </section>`;

      // ── HERO IMAGE BG ─────────────────────────────────────────────────
      renders['hero-image-bg'] = () => `<section ${id} class="${cls}" style="${wrapStyle};background-image:url(${s.imageUrl||'https://picsum.photos/1600/800?random=102'});background-size:cover;background-position:center;padding:${s.pt||120}px 40px ${s.pb||120}px;position:relative;text-align:center;">
        <div style="position:absolute;inset:0;background:rgba(0,0,0,${s.overlay||0.5});"></div>
        <div style="position:relative;max-width:700px;margin:0 auto;color:#fff;">
          <h1 style="font-size:54px;font-weight:900;line-height:1.12;margin:0 0 20px;">${(s.headline||'Create Something Amazing').replace(/\\n/g,'<br>')}</h1>
          <p style="font-size:18px;opacity:0.85;margin:0 0 32px;line-height:1.7;">${s.subtext||'Join 50,000+ creators building the web of tomorrow.'}</p>
          <a href="${s.ctaLink||'#'}" style="background:#0ea5e9;color:#fff;padding:16px 36px;border-radius:10px;text-decoration:none;font-size:16px;font-weight:700;display:inline-block;">${s.ctaText||'Start Building'}</a>
        </div>
      </section>`;

      // ── HERO GRADIENT ─────────────────────────────────────────────────
      renders['hero-gradient'] = () => `<section ${id} class="${cls}" style="${wrapStyle};background:linear-gradient(135deg,${s.gradientFrom||'#667eea'},${s.gradientTo||'#764ba2'});padding:${s.pt||100}px 40px ${s.pb||100}px;text-align:center;color:#fff;">
        <div style="max-width:760px;margin:0 auto;">
          <h1 style="font-size:54px;font-weight:900;line-height:1.1;margin:0 0 20px;">${(s.headline||'Scale Your Business Without Limits').replace(/\\n/g,'<br>')}</h1>
          <p style="font-size:18px;opacity:0.85;line-height:1.7;margin:0 0 36px;">${s.subtext||'Powerful tools for modern teams.'}</p>
          <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
            <a href="#" style="background:#fff;color:#667eea;padding:15px 34px;border-radius:10px;text-decoration:none;font-size:16px;font-weight:800;">${s.ctaText||'Try For Free'}</a>
            ${s.ctaSecondary?`<a href="#" style="color:#fff;padding:15px 28px;border-radius:10px;text-decoration:none;font-size:16px;font-weight:600;border:2px solid rgba(255,255,255,0.45);">${s.ctaSecondary}</a>`:''}
          </div>
        </div>
      </section>`;

      // ── HERO MINIMAL ──────────────────────────────────────────────────
      renders['hero-minimal'] = () => `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#fff'};padding:${s.pt||120}px 40px ${s.pb||120}px;text-align:center;">
        <h1 style="font-size:64px;font-weight:900;line-height:1.05;color:#0f172a;margin:0 0 16px;">${(s.headline||'Design Without Boundaries').replace(/\\n/g,'<br>')}</h1>
        <p style="font-size:20px;color:#94a3b8;margin:0 0 36px;">${s.subtext||'Simple. Beautiful. Powerful.'}</p>
        <a href="#" style="background:#0f172a;color:#fff;padding:16px 36px;border-radius:10px;text-decoration:none;font-size:16px;font-weight:700;">${s.ctaText||'Get Started'}</a>
      </section>`;

      // ── HERO SAAS ─────────────────────────────────────────────────────
      renders['hero-saas'] = () => `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#0f172a'};padding:${s.pt||80}px 40px ${s.pb||80}px;color:#fff;">
        <div style="max-width:1200px;margin:0 auto;display:flex;align-items:center;gap:60px;flex-wrap:wrap;">
          <div style="flex:1;min-width:280px;">
            ${s.badge?`<span style="display:inline-block;background:#1e3a5f;color:#38bdf8;padding:5px 14px;border-radius:20px;font-size:12px;font-weight:700;margin-bottom:18px;">${s.badge}</span>`:''}
            <h1 style="font-size:48px;font-weight:900;line-height:1.12;margin:0 0 20px;">${(s.headline||"Your Team's New Command Center").replace(/\\n/g,'<br>')}</h1>
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
      </section>`;

      // ── HERO AGENCY ───────────────────────────────────────────────────
      renders['hero-agency'] = () => `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#0f172a'};padding:${s.pt||100}px 60px ${s.pb||100}px;color:#fff;">
        <div style="max-width:960px;">
          ${s.eyebrow?`<p style="font-size:13px;font-weight:700;letter-spacing:0.15em;text-transform:uppercase;color:${s.accentColor||'#f59e0b'};margin:0 0 16px;">${s.eyebrow}</p>`:''}
          <h1 style="font-size:72px;font-weight:900;line-height:1.04;margin:0 0 32px;">${(s.headline||'We Build<br>Experiences<br>That Matter').replace(/\\n/g,'<br>')}</h1>
          <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <a href="#" style="background:${s.accentColor||'#f59e0b'};color:#000;padding:16px 32px;border-radius:8px;text-decoration:none;font-size:16px;font-weight:800;">${s.ctaText||'Our Work'}</a>
            ${s.ctaSecondary?`<a href="#" style="color:#fff;padding:16px 32px;text-decoration:none;font-size:16px;font-weight:600;border:2px solid rgba(255,255,255,0.2);border-radius:8px;">${s.ctaSecondary}</a>`:''}
          </div>
        </div>
      </section>`;

      // ── FEATURES GRID ─────────────────────────────────────────────────
      renders['features-grid'] = () => {
        const feats = s.features || [
          {icon:'fa-bolt',title:'Lightning Fast',text:'Optimized for speed from day one.'},
          {icon:'fa-shield-alt',title:'Secure by Default',text:'Enterprise-grade security built in.'},
          {icon:'fa-sync',title:'Always Synced',text:'Real-time updates across all devices.'},
          {icon:'fa-chart-line',title:'Smart Analytics',text:'Deep insights to grow faster.'},
          {icon:'fa-users',title:'Team Ready',text:'Built for collaboration at any scale.'},
          {icon:'fa-plug',title:'100+ Integrations',text:'Connects with your favourite tools.'},
        ];
        return `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#fff'};padding:${s.pt||80}px 40px ${s.pb||80}px;text-align:center;">
          <div style="max-width:1100px;margin:0 auto;">
            <h2 style="font-size:40px;font-weight:900;color:#0f172a;margin:0 0 12px;">${s.headline||'Everything You Need'}</h2>
            <p style="font-size:17px;color:#64748b;margin:0 0 56px;">${s.subtext||'Built for modern teams.'}</p>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:28px;text-align:left;">
              ${feats.map(f=>`<div style="padding:28px;border:1px solid #f1f5f9;border-radius:14px;transition:box-shadow 0.2s;" onmouseover="this.style.boxShadow='0 8px 30px rgba(0,0,0,0.08)'" onmouseout="this.style.boxShadow='none'">
                <div style="width:48px;height:48px;background:#eff6ff;border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:16px;">
                  <i class="fas ${f.icon||'fa-star'}" style="color:#0ea5e9;font-size:20px;"></i>
                </div>
                <h3 style="font-size:17px;font-weight:700;margin:0 0 8px;color:#0f172a;">${f.title}</h3>
                <p style="font-size:14px;color:#64748b;margin:0;line-height:1.6;">${f.text}</p>
              </div>`).join('')}
            </div>
          </div>
        </section>`;
      };

      // ── FEATURES ALTERNATING LIST ─────────────────────────────────────
      renders['features-list'] = () => {
        const items = s.items || [
          {title:'Blazing Performance',text:'Our CDN-backed infrastructure handles millions of requests.',imageUrl:'https://picsum.photos/500/350?random=104'},
          {title:'Developer Friendly',text:'Clean APIs, great docs, SDKs for every major language.',imageUrl:'https://picsum.photos/500/350?random=105'},
          {title:'Analytics That Matter',text:'Beautiful dashboards showing metrics that move the needle.',imageUrl:'https://picsum.photos/500/350?random=106'},
        ];
        return `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#fff'};padding:${s.pt||80}px 40px ${s.pb||80}px;">
          <div style="max-width:1100px;margin:0 auto;">
            <h2 style="font-size:40px;font-weight:900;color:#0f172a;text-align:center;margin:0 0 64px;">${s.headline||'Built for scale'}</h2>
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
      };

      // ── FEATURES ICONS ROW ────────────────────────────────────────────
      renders['features-icons'] = () => {
        const items = s.items || [{icon:'fa-rocket',label:'Fast Deploy'},{icon:'fa-lock',label:'Secure'},{icon:'fa-cloud',label:'Cloud Native'},{icon:'fa-mobile-alt',label:'Mobile First'},{icon:'fa-headset',label:'24/7 Support'},{icon:'fa-code',label:'Open API'}];
        return `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#f8fafc'};padding:${s.pt||60}px 40px ${s.pb||60}px;text-align:center;">
          <div style="max-width:1000px;margin:0 auto;">
            <h2 style="font-size:36px;font-weight:900;color:#0f172a;margin:0 0 10px;">${s.headline||'Why Teams Love Us'}</h2>
            <p style="color:#64748b;font-size:16px;margin:0 0 48px;">${s.subtext||''}</p>
            <div style="display:flex;gap:20px;justify-content:center;flex-wrap:wrap;">
              ${items.map(item=>`<div style="display:flex;flex-direction:column;align-items:center;gap:10px;padding:24px 20px;background:#fff;border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,0.06);min-width:100px;transition:transform 0.2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform=''">
                <div style="width:52px;height:52px;background:linear-gradient(135deg,#0ea5e9,#8b5cf6);border-radius:14px;display:flex;align-items:center;justify-content:center;">
                  <i class="fas ${item.icon}" style="color:#fff;font-size:20px;"></i>
                </div>
                <span style="font-size:13px;font-weight:600;color:#374151;">${item.label}</span>
              </div>`).join('')}
            </div>
          </div>
        </section>`;
      };

      // ── FEATURES NUMBERED STEPS ───────────────────────────────────────
      renders['features-numbered'] = () => {
        const steps = s.steps || [{title:'Create Account',text:'Sign up free in 60 seconds.'},{title:'Set Up Project',text:'Configure your workspace in minutes.'},{title:'Launch & Grow',text:'Go live and track results.'}];
        return `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#fff'};padding:${s.pt||80}px 40px ${s.pb||80}px;text-align:center;">
          <div style="max-width:900px;margin:0 auto;">
            <h2 style="font-size:40px;font-weight:900;color:#0f172a;margin:0 0 12px;">${s.headline||'How It Works'}</h2>
            <p style="color:#64748b;font-size:17px;margin:0 0 56px;">${s.subtext||''}</p>
            <div style="display:flex;justify-content:center;flex-wrap:wrap;">
              ${steps.map((step,i)=>`<div style="flex:1;min-width:200px;padding:0 24px;position:relative;">
                <div style="width:52px;height:52px;background:#0ea5e9;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:20px;font-weight:900;color:#fff;">${i+1}</div>
                ${i<steps.length-1?`<div style="position:absolute;top:26px;left:60%;width:80%;height:2px;background:#e2e8f0;z-index:0;"></div>`:''}
                <h3 style="font-size:17px;font-weight:700;color:#0f172a;margin:0 0 8px;">${step.title}</h3>
                <p style="font-size:14px;color:#64748b;margin:0;line-height:1.6;">${step.text}</p>
              </div>`).join('')}
            </div>
          </div>
        </section>`;
      };

      // ── FEATURES CARDS ────────────────────────────────────────────────
      renders['features-cards'] = () => {
        const cards = s.cards || [{icon:'fa-layer-group',title:'Drag & Drop',text:'Build pages visually.',color:'#0ea5e9'},{icon:'fa-paint-roller',title:'Custom Themes',text:'100+ beautiful themes.',color:'#8b5cf6'},{icon:'fa-database',title:'CMS Built-in',text:'Manage content easily.',color:'#f59e0b'},{icon:'fa-chart-pie',title:'Analytics',text:'Track every conversion.',color:'#22c55e'}];
        return `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#fff'};padding:${s.pt||80}px 40px ${s.pb||80}px;">
          <div style="max-width:1100px;margin:0 auto;text-align:center;">
            <h2 style="font-size:40px;font-weight:900;color:#0f172a;margin:0 0 48px;">${s.headline||'Core Features'}</h2>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:24px;">
              ${cards.map(card=>`<div style="padding:32px 24px;border-radius:16px;background:#fff;border:1px solid #f1f5f9;border-top:4px solid ${card.color||'#0ea5e9'};text-align:left;transition:transform 0.2s,box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 40px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                <i class="fas ${card.icon||'fa-star'}" style="font-size:28px;color:${card.color||'#0ea5e9'};margin-bottom:16px;display:block;"></i>
                <h3 style="font-size:18px;font-weight:700;color:#0f172a;margin:0 0 8px;">${card.title}</h3>
                <p style="font-size:14px;color:#64748b;margin:0;line-height:1.6;">${card.text}</p>
              </div>`).join('')}
            </div>
          </div>
        </section>`;
      };

      // ── CONTENT TWO COLUMN ────────────────────────────────────────────
      renders['content-two-col'] = () => {
        const imgLeft = s.imagePosition === 'left';
        return `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#fff'};padding:${s.pt||80}px 40px ${s.pb||80}px;">
          <div style="max-width:1100px;margin:0 auto;display:flex;align-items:center;gap:60px;flex-wrap:wrap;${imgLeft?'flex-direction:row-reverse;':''}">
            <div style="flex:1;min-width:260px;">
              ${s.eyebrow?`<p style="font-size:13px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:#0ea5e9;margin:0 0 10px;">${s.eyebrow}</p>`:''}
              <h2 style="font-size:38px;font-weight:900;color:#0f172a;line-height:1.2;margin:0 0 16px;">${(s.headline||"We're on a Mission").replace(/\\n/g,'<br>')}</h2>
              <div style="font-size:16px;color:#64748b;line-height:1.7;">${s.text||'<p>Your content goes here.</p>'}</div>
              ${s.ctaText?`<a href="#" style="display:inline-block;margin-top:24px;background:#0ea5e9;color:#fff;padding:12px 26px;border-radius:8px;text-decoration:none;font-size:15px;font-weight:700;">${s.ctaText} →</a>`:''}
            </div>
            <div style="flex:1;min-width:260px;">
              <img src="${s.imageUrl||'https://picsum.photos/600/450?random=107'}" style="width:100%;border-radius:16px;box-shadow:0 16px 48px rgba(0,0,0,0.1);">
            </div>
          </div>
        </section>`;
      };

      // ── CONTENT STATS ROW ─────────────────────────────────────────────
      renders['content-stats'] = () => {
        const stats = s.stats || [{value:'50K+',label:'Active Users'},{value:'98%',label:'Uptime SLA'},{value:'4.9★',label:'Rating'},{value:'150+',label:'Countries'}];
        return `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#0ea5e9'};padding:${s.pt||60}px 40px ${s.pb||60}px;text-align:center;">
          <div style="max-width:1000px;margin:0 auto;">
            ${s.headline?`<h2 style="font-size:32px;font-weight:800;color:${s.textColor||'#fff'};margin:0 0 40px;">${s.headline}</h2>`:''}
            <div style="display:flex;justify-content:center;flex-wrap:wrap;">
              ${stats.map((stat,i)=>`<div style="flex:1;min-width:160px;padding:20px;${i>0?'border-left:1px solid rgba(255,255,255,0.2)':''}">
                <div style="font-size:48px;font-weight:900;color:${s.textColor||'#fff'};line-height:1;">${stat.value}</div>
                <p style="font-size:14px;color:rgba(255,255,255,0.8);margin:8px 0 0;font-weight:500;">${stat.label}</p>
              </div>`).join('')}
            </div>
          </div>
        </section>`;
      };

      // ── CONTENT TIMELINE ──────────────────────────────────────────────
      renders['content-timeline'] = () => {
        const events = s.events || [{year:'2020',title:'Founded',text:'Started in a garage.'},{year:'2021',title:'1K Users',text:'First milestone.'},{year:'2022',title:'Funding',text:'Raised $5M.'},{year:'2024',title:'100K Users',text:'Crossed the milestone.'}];
        return `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#f8fafc'};padding:${s.pt||80}px 40px ${s.pb||80}px;">
          <div style="max-width:700px;margin:0 auto;text-align:center;">
            <h2 style="font-size:40px;font-weight:900;color:#0f172a;margin:0 0 56px;">${s.headline||'Our Journey'}</h2>
            <div style="position:relative;">
              <div style="position:absolute;left:50%;transform:translateX(-50%);top:0;bottom:0;width:2px;background:#e2e8f0;"></div>
              ${events.map((ev,i)=>`<div style="display:flex;gap:28px;align-items:flex-start;margin-bottom:40px;flex-direction:${i%2===0?'row-reverse':'row'};">
                <div style="flex:1;text-align:${i%2===0?'right':'left'};">
                  <span style="font-size:11px;font-weight:700;color:#0ea5e9;letter-spacing:0.05em;">${ev.year}</span>
                  <h3 style="font-size:17px;font-weight:700;color:#0f172a;margin:4px 0 6px;">${ev.title}</h3>
                  <p style="font-size:14px;color:#64748b;margin:0;">${ev.text}</p>
                </div>
                <div style="width:16px;height:16px;background:#0ea5e9;border-radius:50%;flex-shrink:0;margin-top:18px;position:relative;z-index:1;box-shadow:0 0 0 4px #fff;"></div>
                <div style="flex:1;"></div>
              </div>`).join('')}
            </div>
          </div>
        </section>`;
      };

      // ── CONTENT CTA STRIP ─────────────────────────────────────────────
      renders['content-cta-strip'] = () => `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#0ea5e9'};padding:${s.pt||60}px 40px ${s.pb||60}px;text-align:center;">
        <div style="max-width:680px;margin:0 auto;">
          <h2 style="font-size:38px;font-weight:900;color:#fff;margin:0 0 12px;">${s.headline||'Ready to get started?'}</h2>
          <p style="font-size:17px;color:rgba(255,255,255,0.85);margin:0 0 32px;">${s.subtext||'Join 50,000+ businesses growing with us.'}</p>
          <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
            <a href="${s.ctaLink||'#'}" style="background:#fff;color:#0ea5e9;padding:14px 32px;border-radius:10px;text-decoration:none;font-size:16px;font-weight:800;">${s.ctaText||'Start Free Trial'}</a>
            ${s.ctaSecondary?`<a href="#" style="color:#fff;padding:14px 28px;border-radius:10px;text-decoration:none;font-size:16px;font-weight:600;border:2px solid rgba(255,255,255,0.45);">${s.ctaSecondary}</a>`:''}
          </div>
        </div>
      </section>`;

      // ── CONTENT FAQ ───────────────────────────────────────────────────
      renders['content-faq'] = () => {
        const items = s.items || [{q:'Is there a free plan?',a:'Yes! Our free plan includes everything to get started.'},{q:'Can I upgrade anytime?',a:'Absolutely. Change plans anytime, we prorate the difference.'},{q:'Do you offer refunds?',a:'We offer a 30-day money-back guarantee. No questions asked.'},{q:'Is my data safe?',a:'Data is encrypted at rest and in transit. SOC2 Type II certified.'}];
        return `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#fff'};padding:${s.pt||80}px 40px ${s.pb||80}px;">
          <div style="max-width:720px;margin:0 auto;text-align:center;">
            <h2 style="font-size:40px;font-weight:900;color:#0f172a;margin:0 0 12px;">${s.headline||'Frequently Asked Questions'}</h2>
            <p style="color:#64748b;font-size:16px;margin:0 0 48px;">${s.subtext||''}</p>
            <div style="text-align:left;">
              ${items.map((item,i)=>`<div style="border-bottom:1px solid #f1f5f9;">
                <button onclick="const c=this.nextElementSibling;c.style.display=c.style.display==='block'?'none':'block'" style="width:100%;text-align:left;padding:18px 0;background:none;border:none;font-size:16px;font-weight:600;color:#0f172a;cursor:pointer;display:flex;justify-content:space-between;align-items:center;">
                  ${item.q} <i class="fas fa-chevron-down" style="color:#94a3b8;font-size:12px;flex-shrink:0;"></i>
                </button>
                <div style="display:${i===0?'block':'none'};padding-bottom:16px;font-size:15px;color:#64748b;line-height:1.7;">${item.a}</div>
              </div>`).join('')}
            </div>
          </div>
        </section>`;
      };

      // ── TESTIMONIALS GRID ─────────────────────────────────────────────
      renders['testimonials-grid'] = () => {
        const items = s.items || [
          {text:'This tool completely changed how our team works.',author:'Sarah Johnson',role:'CTO at TechFlow',photo:'https://i.pravatar.cc/80?img=1',rating:5},
          {text:'The best investment we made this year.',author:'Mike Chen',role:'Product Lead',photo:'https://i.pravatar.cc/80?img=3',rating:5},
          {text:"Nothing comes close to the simplicity and power.",author:'Emma Davis',role:'Founder at Bloom',photo:'https://i.pravatar.cc/80?img=5',rating:5},
        ];
        return `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#f8fafc'};padding:${s.pt||80}px 40px ${s.pb||80}px;text-align:center;">
          <div style="max-width:1100px;margin:0 auto;">
            <h2 style="font-size:40px;font-weight:900;color:#0f172a;margin:0 0 10px;">${s.headline||'Loved by thousands'}</h2>
            <p style="color:#64748b;font-size:17px;margin:0 0 48px;">${s.subtext||"Don't take our word for it."}</p>
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
      };

      // ── LOGOS ROW ─────────────────────────────────────────────────────
      renders['logos-row'] = () => {
        const logos = s.logos || ['Google','Microsoft','Stripe','Shopify','Notion','Figma','Vercel'];
        return `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#fff'};padding:${s.pt||50}px 40px ${s.pb||50}px;text-align:center;">
          ${s.headline?`<p style="font-size:12px;font-weight:600;letter-spacing:0.1em;text-transform:uppercase;color:#94a3b8;margin:0 0 24px;">${s.headline}</p>`:''}
          <div style="display:flex;gap:40px;align-items:center;justify-content:center;flex-wrap:wrap;">
            ${logos.map(logo=>`<div style="font-size:20px;font-weight:900;color:#cbd5e1;letter-spacing:-1px;transition:color 0.2s;cursor:default;" onmouseover="this.style.color='#64748b'" onmouseout="this.style.color='#cbd5e1'">${logo}</div>`).join('')}
          </div>
        </section>`;
      };

      // ── RATING SUMMARY ────────────────────────────────────────────────
      renders['rating-summary'] = () => {
        const breakdown = s.breakdown || [{stars:5,pct:82},{stars:4,pct:12},{stars:3,pct:4},{stars:2,pct:1},{stars:1,pct:1}];
        return `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#f8fafc'};padding:${s.pt||60}px 40px ${s.pb||60}px;text-align:center;">
          <div style="max-width:480px;margin:0 auto;">
            <h2 style="font-size:36px;font-weight:900;color:#0f172a;margin:0 0 8px;">${s.headline||'What our users say'}</h2>
            <div style="font-size:64px;font-weight:900;color:#f59e0b;line-height:1;margin:24px 0 4px;">${s.average||'4.9'}</div>
            <div style="display:flex;justify-content:center;gap:4px;margin-bottom:6px;">${Array(5).fill('').map(()=>'<i class="fas fa-star" style="color:#f59e0b;font-size:20px;"></i>').join('')}</div>
            <p style="color:#94a3b8;font-size:14px;margin:0 0 28px;">Based on ${s.total||'12,500'} reviews</p>
            ${breakdown.map(row=>`<div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
              <span style="font-size:13px;color:#64748b;width:28px;text-align:right;">${row.stars}★</span>
              <div style="flex:1;height:8px;background:#e2e8f0;border-radius:4px;overflow:hidden;">
                <div style="width:${row.pct}%;height:100%;background:#f59e0b;border-radius:4px;"></div>
              </div>
              <span style="font-size:12px;color:#94a3b8;width:30px;">${row.pct}%</span>
            </div>`).join('')}
          </div>
        </section>`;
      };

      // ── FOOTER SIMPLE ─────────────────────────────────────────────────
      renders['footer-simple'] = () => {
        const links = (s.links||'Home,About,Services,Blog,Contact').split(',');
        return `<footer ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#1e293b'};padding:${s.pt||40}px 40px ${s.pb||30}px;">
          <div style="max-width:1100px;margin:0 auto;">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;padding-bottom:24px;border-bottom:1px solid rgba(255,255,255,0.08);">
              <div>
                <div style="font-size:20px;font-weight:800;color:#fff;">${s.logo||'MySite'}</div>
                ${s.tagline?`<p style="font-size:13px;color:${s.textColor||'#94a3b8'};margin:4px 0 0;">${s.tagline}</p>`:''}
              </div>
              <div style="display:flex;gap:24px;flex-wrap:wrap;">
                ${links.map(l=>`<a href="#" style="color:${s.textColor||'#94a3b8'};text-decoration:none;font-size:14px;transition:color 0.15s;" onmouseover="this.style.color='#e2e8f0'" onmouseout="this.style.color='${s.textColor||'#94a3b8'}'">${l.trim()}</a>`).join('')}
              </div>
            </div>
            <p style="color:${s.textColor||'#475569'};font-size:13px;margin:20px 0 0;text-align:center;">${s.copyright||'© 2026 MySite. All rights reserved.'}</p>
          </div>
        </footer>`;
      };

      // ── FOOTER FOUR COLUMN ────────────────────────────────────────────
      renders['footer-four-col'] = () => {
        const col = (title, linksStr) => `<div>
          <h4 style="font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#fff;margin:0 0 14px;">${title}</h4>
          ${(linksStr||'').split(',').map(l=>`<a href="#" style="display:block;color:${s.textColor||'#94a3b8'};text-decoration:none;font-size:14px;margin-bottom:8px;transition:color 0.15s;" onmouseover="this.style.color='#e2e8f0'" onmouseout="this.style.color='${s.textColor||'#94a3b8'}'">${l.trim()}</a>`).join('')}
        </div>`;
        return `<footer ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#0f172a'};padding:${s.pt||60}px 40px ${s.pb||40}px;">
          <div style="max-width:1100px;margin:0 auto;">
            <div style="display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:40px;margin-bottom:48px;">
              <div>
                <div style="font-size:22px;font-weight:800;color:#fff;margin-bottom:10px;">${s.logo||'MySite'}</div>
                <p style="font-size:14px;color:${s.textColor||'#94a3b8'};line-height:1.6;margin:0;">${s.tagline||'The platform for modern teams.'}</p>
              </div>
              ${col(s.col1Title||'Product', s.col1Links||'Features,Pricing,Changelog,Roadmap')}
              ${col(s.col2Title||'Company', s.col2Links||'About,Blog,Careers,Press')}
              ${col(s.col3Title||'Legal',   s.col3Links||'Privacy,Terms,Cookies')}
            </div>
            <div style="border-top:1px solid rgba(255,255,255,0.06);padding-top:24px;text-align:center;">
              <p style="color:${s.textColor||'#475569'};font-size:13px;margin:0;">${s.copyright||'© 2026 MySite Inc.'}</p>
            </div>
          </div>
        </footer>`;
      };

      // ── FOOTER DARK (newsletter) ──────────────────────────────────────
      renders['footer-dark'] = () => {
        const col = (title, linksStr) => `<div>
          <h4 style="font-size:12px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:#94a3b8;margin:0 0 14px;">${title}</h4>
          ${(linksStr||'').split(',').map(l=>`<a href="#" style="display:block;color:#64748b;text-decoration:none;font-size:13px;margin-bottom:8px;" onmouseover="this.style.color='#e2e8f0'" onmouseout="this.style.color='#64748b'">${l.trim()}</a>`).join('')}
        </div>`;
        return `<footer ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#020617'};padding:${s.pt||80}px 40px ${s.pb||40}px;">
          <div style="max-width:1100px;margin:0 auto;">
            <div style="display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:40px;margin-bottom:60px;flex-wrap:wrap;">
              <div>
                <div style="font-size:22px;font-weight:800;color:#fff;margin-bottom:10px;">${s.logo||'MySite'}</div>
                <p style="font-size:14px;color:#64748b;line-height:1.6;margin:0 0 24px;">${s.tagline||'The all-in-one platform.'}</p>
                ${s.ctaHeadline?`<p style="font-size:13px;font-weight:600;color:#94a3b8;margin:0 0 10px;">${s.ctaHeadline}</p>
                <div style="display:flex;max-width:280px;">
                  <input placeholder="${s.placeholder||'Your email'}" style="flex:1;padding:10px 12px;background:#0f172a;border:1px solid #1e293b;border-right:none;border-radius:8px 0 0 8px;color:#e2e8f0;font-size:13px;outline:none;">
                  <button style="padding:10px 16px;background:#0ea5e9;color:#fff;border:none;border-radius:0 8px 8px 0;font-size:13px;font-weight:600;cursor:pointer;">${s.btnText||'Go'}</button>
                </div>`:''}
              </div>
              ${col(s.col1Title||'Platform',  s.col1Links||'Dashboard,Analytics,API')}
              ${col(s.col2Title||'Resources', s.col2Links||'Docs,Blog,Status')}
              ${col(s.col3Title||'Company',   s.col3Links||'About,Team,Press')}
            </div>
            <div style="border-top:1px solid #0f172a;padding-top:24px;display:flex;justify-content:space-between;flex-wrap:wrap;gap:12px;align-items:center;">
              <p style="color:#334155;font-size:13px;margin:0;">${s.copyright||'© 2026 MySite. All rights reserved.'}</p>
              <div style="display:flex;gap:14px;">
                <a href="#" style="color:#334155;font-size:16px;transition:color 0.15s;" onmouseover="this.style.color='#94a3b8'" onmouseout="this.style.color='#334155'"><i class="fab fa-twitter"></i></a>
                <a href="#" style="color:#334155;font-size:16px;transition:color 0.15s;" onmouseover="this.style.color='#94a3b8'" onmouseout="this.style.color='#334155'"><i class="fab fa-github"></i></a>
                <a href="#" style="color:#334155;font-size:16px;transition:color 0.15s;" onmouseover="this.style.color='#94a3b8'" onmouseout="this.style.color='#334155'"><i class="fab fa-linkedin"></i></a>
              </div>
            </div>
          </div>
        </footer>`;
      };

      // ── FOOTER MINIMAL ────────────────────────────────────────────────
      renders['footer-minimal'] = () => {
        const links = (s.links||'Privacy,Terms,Contact').split(',');
        return `<footer ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#f8fafc'};padding:${s.pt||28}px 40px ${s.pb||28}px;">
          <div style="max-width:1100px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
            <span style="font-weight:700;color:#94a3b8;">${s.logo||'MySite'}</span>
            <p style="font-size:13px;color:${s.textColor||'#94a3b8'};margin:0;">${s.copyright||'© 2026 MySite'}</p>
            <div style="display:flex;gap:20px;">
              ${links.map(l=>`<a href="#" style="color:${s.textColor||'#94a3b8'};text-decoration:none;font-size:13px;">${l.trim()}</a>`).join('')}
            </div>
          </div>
        </footer>`;
      };

      // ── NEWSLETTER INLINE ─────────────────────────────────────────────
      renders['newsletter-inline'] = () => `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#f8fafc'};padding:${s.pt||60}px 40px ${s.pb||60}px;text-align:center;">
        <div style="max-width:560px;margin:0 auto;">
          <h2 style="font-size:32px;font-weight:900;color:#0f172a;margin:0 0 8px;">${s.headline||'Stay in the loop'}</h2>
          <p style="color:#64748b;font-size:16px;margin:0 0 28px;">${s.subtext||'Get product updates and tips delivered to your inbox.'}</p>
          <div style="display:flex;gap:0;max-width:420px;margin:0 auto;">
            <input type="email" placeholder="${s.placeholder||'Enter your email'}" style="flex:1;padding:13px 16px;border:1px solid #e2e8f0;border-right:none;border-radius:10px 0 0 10px;font-size:15px;outline:none;">
            <button style="padding:13px 24px;background:#0ea5e9;color:#fff;border:none;border-radius:0 10px 10px 0;font-size:15px;font-weight:700;cursor:pointer;">${s.buttonText||'Subscribe'}</button>
          </div>
          ${s.disclaimer?`<p style="font-size:12px;color:#94a3b8;margin:12px 0 0;">${s.disclaimer}</p>`:''}
        </div>
      </section>`;

      // ── PRICING THREE COL ─────────────────────────────────────────────
      renders['pricing-three-col'] = () => {
        const plans = s.plans || [
          {name:'Free',price:'0',period:'/mo',features:'3 Projects,1GB Storage,Community Support',cta:'Get Started',highlight:false},
          {name:'Pro',price:'29',period:'/mo',features:'Unlimited Projects,50GB Storage,Priority Support,Analytics,Custom Domain',cta:'Start Free Trial',highlight:true},
          {name:'Enterprise',price:'99',period:'/mo',features:'Everything in Pro,SSO & SAML,99.9% SLA,Dedicated Manager,Custom Contracts',cta:'Contact Sales',highlight:false},
        ];
        return `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#f8fafc'};padding:${s.pt||80}px 40px ${s.pb||80}px;text-align:center;">
          <div style="max-width:1050px;margin:0 auto;">
            <h2 style="font-size:40px;font-weight:900;color:#0f172a;margin:0 0 12px;">${s.headline||'Simple, Transparent Pricing'}</h2>
            <p style="color:#64748b;font-size:17px;margin:0 0 52px;">${s.subtext||'No hidden fees. Cancel anytime.'}</p>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:24px;align-items:stretch;">
              ${plans.map(plan=>`<div style="background:#fff;border-radius:20px;padding:36px 28px;border:${plan.highlight?'2px solid #0ea5e9':'1px solid #f1f5f9'};box-shadow:${plan.highlight?'0 12px 40px rgba(14,165,233,0.15)':'0 2px 12px rgba(0,0,0,0.04)'};position:relative;${plan.highlight?'transform:scale(1.04)':''}">
                ${plan.highlight?`<div style="position:absolute;top:-14px;left:50%;transform:translateX(-50%);background:#0ea5e9;color:#fff;padding:5px 18px;border-radius:20px;font-size:12px;font-weight:700;">MOST POPULAR</div>`:''}
                <h3 style="font-size:18px;font-weight:700;color:#0f172a;margin:0 0 16px;">${plan.name}</h3>
                <div style="font-size:52px;font-weight:900;color:${plan.highlight?'#0ea5e9':'#0f172a'};line-height:1;">$${plan.price}<span style="font-size:16px;font-weight:400;color:#94a3b8;">${plan.period}</span></div>
                <ul style="list-style:none;padding:0;margin:24px 0;text-align:left;">
                  ${plan.features.split(',').map(f=>`<li style="display:flex;align-items:center;gap:8px;padding:6px 0;font-size:14px;color:#475569;"><i class="fas fa-check-circle" style="color:#22c55e;flex-shrink:0;"></i>${f.trim()}</li>`).join('')}
                </ul>
                <a href="#" style="display:block;padding:12px;background:${plan.highlight?'#0ea5e9':'transparent'};color:${plan.highlight?'#fff':'#0ea5e9'};border:2px solid #0ea5e9;border-radius:10px;text-decoration:none;font-size:15px;font-weight:700;">${plan.cta}</a>
              </div>`).join('')}
            </div>
          </div>
        </section>`;
      };

      // ── TEAM GRID ─────────────────────────────────────────────────────
      renders['team-grid'] = () => {
        const members = s.members || [
          {photo:'https://i.pravatar.cc/200?img=10',name:'Alex Morgan',role:'CEO & Co-Founder',twitter:'#',linkedin:'#'},
          {photo:'https://i.pravatar.cc/200?img=11',name:'Jamie Reeves',role:'CTO',twitter:'#',linkedin:'#'},
          {photo:'https://i.pravatar.cc/200?img=12',name:'Sam Taylor',role:'Head of Design',twitter:'#',linkedin:'#'},
          {photo:'https://i.pravatar.cc/200?img=13',name:'Chris Kim',role:'Lead Engineer',twitter:'#',linkedin:'#'},
        ];
        return `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#fff'};padding:${s.pt||80}px 40px ${s.pb||80}px;text-align:center;">
          <div style="max-width:1100px;margin:0 auto;">
            <h2 style="font-size:40px;font-weight:900;color:#0f172a;margin:0 0 10px;">${s.headline||'Meet the Team'}</h2>
            <p style="color:#64748b;font-size:17px;margin:0 0 52px;">${s.subtext||'The people behind the product.'}</p>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:32px;">
              ${members.map(m=>`<div style="text-align:center;">
                <img src="${m.photo}" style="width:96px;height:96px;border-radius:50%;object-fit:cover;margin:0 auto 14px;">
                <h3 style="font-size:16px;font-weight:700;color:#0f172a;margin:0 0 4px;">${m.name}</h3>
                <p style="font-size:14px;color:#64748b;margin:0 0 12px;">${m.role}</p>
                <div style="display:flex;justify-content:center;gap:10px;">
                  <a href="${m.twitter||'#'}" style="color:#94a3b8;font-size:15px;"><i class="fab fa-twitter"></i></a>
                  <a href="${m.linkedin||'#'}" style="color:#94a3b8;font-size:15px;"><i class="fab fa-linkedin"></i></a>
                </div>
              </div>`).join('')}
            </div>
          </div>
        </section>`;
      };

      // ── CONTACT SPLIT ─────────────────────────────────────────────────
      renders['contact-split'] = () => `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#fff'};padding:${s.pt||80}px 40px ${s.pb||80}px;">
        <div style="max-width:1100px;margin:0 auto;display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:start;flex-wrap:wrap;">
          <div>
            <h2 style="font-size:38px;font-weight:900;color:#0f172a;margin:0 0 12px;">${s.headline||"Get in Touch"}</h2>
            <p style="color:#64748b;font-size:16px;line-height:1.7;margin:0 0 32px;">${s.subtext||"Have a question or want to work together? We'd love to hear from you."}</p>
            <div style="space-y:16px;">
              <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
                <div style="width:40px;height:40px;background:#eff6ff;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas fa-envelope" style="color:#0ea5e9;font-size:16px;"></i></div>
                <span style="color:#374151;font-size:15px;">${s.email||'hello@mysite.com'}</span>
              </div>
              <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
                <div style="width:40px;height:40px;background:#eff6ff;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas fa-phone" style="color:#0ea5e9;font-size:16px;"></i></div>
                <span style="color:#374151;font-size:15px;">${s.phone||'+1 (555) 000-0000'}</span>
              </div>
              <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:40px;height:40px;background:#eff6ff;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas fa-map-marker-alt" style="color:#0ea5e9;font-size:16px;"></i></div>
                <span style="color:#374151;font-size:15px;">${s.address||'123 Main St, San Francisco, CA'}</span>
              </div>
            </div>
          </div>
          <div style="background:#f8fafc;border-radius:16px;padding:36px;">
            <div style="display:flex;flex-direction:column;gap:14px;">
              <input type="text" placeholder="Full Name" style="width:100%;padding:12px 14px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;box-sizing:border-box;outline:none;">
              <input type="email" placeholder="Email Address" style="width:100%;padding:12px 14px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;box-sizing:border-box;outline:none;">
              <input type="text" placeholder="Subject" style="width:100%;padding:12px 14px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;box-sizing:border-box;outline:none;">
              <textarea rows="4" placeholder="Your message..." style="width:100%;padding:12px 14px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;box-sizing:border-box;resize:none;outline:none;"></textarea>
              <button style="padding:13px;background:#0ea5e9;color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:700;cursor:pointer;">${s.submitText||'Send Message'}</button>
            </div>
          </div>
        </div>
      </section>`;

      // ── PORTFOLIO GRID ────────────────────────────────────────────────
      renders['portfolio-grid'] = () => {
        const projects = s.projects || [
          {title:'Brand Identity',cat:'Branding',img:'https://picsum.photos/400/300?random=200'},
          {title:'E-Commerce App',cat:'Development',img:'https://picsum.photos/400/400?random=201'},
          {title:'Marketing Site',cat:'Web Design',img:'https://picsum.photos/400/280?random=202'},
          {title:'Mobile App UI',cat:'UI/UX',img:'https://picsum.photos/400/350?random=203'},
          {title:'Dashboard',cat:'Product',img:'https://picsum.photos/400/300?random=204'},
          {title:'Logo Suite',cat:'Branding',img:'https://picsum.photos/400/320?random=205'},
        ];
        return `<section ${id} class="${cls}" style="${wrapStyle};background:${s.bgColor||'#fff'};padding:${s.pt||80}px 40px ${s.pb||80}px;">
          <div style="max-width:1100px;margin:0 auto;text-align:center;">
            <h2 style="font-size:40px;font-weight:900;color:#0f172a;margin:0 0 48px;">${s.headline||'Selected Work'}</h2>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:20px;">
              ${projects.map(p=>`<div style="position:relative;overflow:hidden;border-radius:14px;cursor:pointer;" onmouseover="this.querySelector('.overlay').style.opacity='1'" onmouseout="this.querySelector('.overlay').style.opacity='0'">
                <img src="${p.img}" style="width:100%;height:220px;object-fit:cover;display:block;transition:transform 0.4s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform=''">
                <div class="overlay" style="position:absolute;inset:0;background:rgba(15,23,42,0.75);display:flex;flex-direction:column;align-items:center;justify-content:center;opacity:0;transition:opacity 0.3s;">
                  <p style="font-size:11px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:#0ea5e9;margin:0 0 6px;">${p.cat}</p>
                  <h3 style="font-size:18px;font-weight:800;color:#fff;margin:0;">${p.title}</h3>
                </div>
              </div>`).join('')}
            </div>
          </div>
        </section>`;
      };

      // ══════════════════════════════════════════════════════════════════
      // Also add defaults for all new widget types
      // ══════════════════════════════════════════════════════════════════

      const renderer = renders[comp.type];
      return renderer
        ? renderer()
        : `<div class="p-4 bg-red-50 text-red-500 text-sm rounded">Unknown widget: ${comp.type}</div>`;
    },

    renderChildren(comp) {
      if (!comp.children || comp.children.length === 0) {
        return `<div style="min-height:60px;border:2px dashed #cbd5e1;border-radius:4px;display:flex;align-items:center;justify-content:center;color:#94a3b8;font-size:12px;padding:16px;">Drop widgets here</div>`;
      }
      return comp.children.map(c => this.renderWidget(c)).join('');
    },

    // =====================================================================
    // SETTINGS PANEL
    // =====================================================================
    renderSettingsPanel() {
      const w = this.selectedWidget();
      if (!w) return '';
      const s = w.settings;

      // field() helper — builds a settings control from a type string
      const field = (label, key, type = 'text', opts = '') => {
        if (type === 'color') {
          return `<div class="flex items-center gap-2 mb-2">
            <label class="text-xs text-gray-500 w-24 shrink-0">${label}</label>
            <input type="color" x-model="getSelectedWidget().settings.${key}" @change="pushHistory();markDirty()" class="w-8 h-8 rounded border-0 cursor-pointer">
            <span class="text-xs font-mono text-gray-400" x-text="getSelectedWidget().settings.${key}"></span>
          </div>`;
        }
        if (type === 'select') {
          return `<div class="mb-2">
            <label class="text-xs text-gray-500 block mb-1">${label}</label>
            <select x-model="getSelectedWidget().settings.${key}" @change="pushHistory();markDirty()" class="w-full border dark:border-gray-600 rounded px-2 py-1.5 text-xs dark:bg-gray-800">${opts}</select>
          </div>`;
        }
        if (type === 'textarea') {
          return `<div class="mb-2">
            <label class="text-xs text-gray-500 block mb-1">${label}</label>
            <textarea x-model="getSelectedWidget().settings.${key}" @change="pushHistory();markDirty()" rows="3" class="w-full border dark:border-gray-600 rounded px-2 py-1.5 text-xs dark:bg-gray-800 resize-none font-mono">${opts}</textarea>
          </div>`;
        }
        if (type === 'checkbox') {
          return `<label class="flex items-center gap-2 mb-2 text-xs">
            <input type="checkbox" x-model="getSelectedWidget().settings.${key}" @change="pushHistory();markDirty()" class="rounded">
            <span class="text-gray-600 dark:text-gray-400">${label}</span>
          </label>`;
        }
        if (type === 'image') {
          return `<div class="mb-2">
            <label class="text-xs text-gray-500 block mb-1">${label}</label>
            <div class="flex gap-1">
              <input type="text" x-model="getSelectedWidget().settings.${key}" @change="pushHistory();markDirty()" placeholder="URL or browse..." class="flex-1 border dark:border-gray-600 rounded px-2 py-1.5 text-xs dark:bg-gray-800">
              <button @click="openMediaLibrary('${key}')" class="px-2 py-1 bg-gray-200 dark:bg-gray-700 rounded text-xs hover:bg-gray-300">📁</button>
            </div>
            <template x-if="getSelectedWidget().settings.${key}">
              <img :src="getSelectedWidget().settings.${key}" class="mt-1 w-full h-20 object-cover rounded border dark:border-gray-600">
            </template>
          </div>`;
        }
        if (type === 'number') {
          return `<div class="mb-2">
            <label class="text-xs text-gray-500 block mb-1">${label}</label>
            <input type="number" x-model.number="getSelectedWidget().settings.${key}" @change="pushHistory();markDirty()" class="w-full border dark:border-gray-600 rounded px-2 py-1.5 text-xs dark:bg-gray-800" ${opts}>
          </div>`;
        }
        if (type === 'range') {
          const [min, max, step] = opts.split(',');
          return `<div class="mb-2">
            <label class="text-xs text-gray-500 flex justify-between mb-1"><span>${label}</span><span x-text="getSelectedWidget().settings.${key}"></span></label>
            <input type="range" min="${min||0}" max="${max||100}" step="${step||1}" x-model.number="getSelectedWidget().settings.${key}" @change="pushHistory();markDirty()" class="w-full accent-brand-500">
          </div>`;
        }
        // Default: text / url / datetime-local
        return `<div class="mb-2">
          <label class="text-xs text-gray-500 block mb-1">${label}</label>
          <input type="${type}" x-model="getSelectedWidget().settings.${key}" @change="pushHistory();markDirty()" class="w-full border dark:border-gray-600 rounded px-2 py-1.5 text-xs dark:bg-gray-800" placeholder="${opts}">
        </div>`;
      };

      const panels = {
        heading: `
          ${field('Text',        'text',       'text',   'Heading text')}
          ${field('Tag',         'tag',        'select', '<option value="h1">H1</option><option value="h2">H2</option><option value="h3">H3</option><option value="h4">H4</option><option value="h5">H5</option><option value="h6">H6</option>')}
          ${field('Alignment',   'alignment',  'select', '<option value="left">Left</option><option value="center">Center</option><option value="right">Right</option>')}
          ${field('Color',       'color',      'color')}
          ${field('Font Size',   'fontSize',   'range',  '12,96,1')}
          ${field('Font Weight', 'fontWeight', 'select', '<option value="400">Normal</option><option value="600">Semi-Bold</option><option value="700">Bold</option><option value="900">Black</option>')}
        `,
        paragraph: `
          <div class="mb-2">
            <label class="text-xs text-gray-500 block mb-1">Content <span class="text-gray-400">(double-click on canvas to edit)</span></label>
            <div x-html="getSelectedWidget().settings.content" class="text-xs p-2 bg-gray-50 dark:bg-gray-800 rounded border dark:border-gray-700 min-h-[60px]"></div>
          </div>
          ${field('Alignment', 'alignment', 'select', '<option value="left">Left</option><option value="center">Center</option><option value="right">Right</option><option value="justify">Justify</option>')}
        `,
        button: `
          ${field('Text',          'text',         'text',   'Button text')}
          ${field('Link URL',      'link',         'url',    'https://')}
          ${field('Background',    'bgColor',      'color')}
          ${field('Text Color',    'textColor',    'color')}
          ${field('Border Radius', 'borderRadius', 'range',  '0,40,1')}
          ${field('Size',          'size',         'select', '<option value="sm">Small</option><option value="md">Medium</option><option value="lg">Large</option>')}
          ${field('Alignment',     'alignment',    'select', '<option value="left">Left</option><option value="center">Center</option><option value="right">Right</option>')}
        `,
        image: `
          ${field('Image URL',  'url',       'image')}
          ${field('Alt Text',   'alt',       'text',  'Description')}
          ${field('Width %',    'width',     'range', '10,100,5')}
          ${field('Alignment',  'alignment', 'select','<option value="left">Left</option><option value="center">Center</option><option value="right">Right</option>')}
          ${field('Link URL',   'link',      'url',   'https://')}
        `,
        video: `
          ${field('Embed URL',     'url',      'url',    'YouTube/Vimeo embed URL')}
          ${field('Aspect Ratio',  'ratio',    'select', '<option value="16/9">16:9</option><option value="4/3">4:3</option><option value="1/1">1:1</option>')}
          ${field('Autoplay',      'autoplay', 'checkbox')}
          ${field('Show Controls', 'controls', 'checkbox')}
        `,
        icon: `
          ${field('Icon Class', 'iconClass', 'text',   'fas fa-star')}
          ${field('Size (px)',  'size',      'range',  '12,120,4')}
          ${field('Color',      'color',     'color')}
          ${field('Link URL',   'link',      'url',    'https://')}
          ${field('Alignment',  'alignment', 'select', '<option value="left">Left</option><option value="center">Center</option><option value="right">Right</option>')}
        `,
        section: `
          ${field('Background Color', 'bgColor',      'color')}
          ${field('Background Image', 'bgImage',      'image')}
          ${field('Padding Top',      'paddingTop',   'range', '0,200,4')}
          ${field('Padding Bottom',   'paddingBottom','range', '0,200,4')}
        `,
        spacer:  `${field('Height (px)', 'height', 'range', '0,300,4')}`,
        divider: `
          ${field('Style',       'style',     'select', '<option value="solid">Solid</option><option value="dashed">Dashed</option><option value="dotted">Dotted</option>')}
          ${field('Color',       'color',     'color')}
          ${field('Width (%)',   'width',     'range',  '10,100,5')}
          ${field('Thickness',   'thickness', 'range',  '1,10,1')}
          ${field('Alignment',   'alignment', 'select', '<option value="left">Left</option><option value="center">Center</option><option value="right">Right</option>')}
        `,
        columns: `
          ${field('Columns', 'columnCount', 'select', '<option value="2">2 Columns</option><option value="3">3 Columns</option><option value="4">4 Columns</option>')}
          ${field('Gap (px)', 'gap',        'range',  '0,60,4')}
        `,
        testimonial: `
          ${field('Quote Text',    'text',   'textarea')}
          ${field('Author Name',   'author', 'text',  'Jane Smith')}
          ${field('Role/Company',  'role',   'text',  'CEO, Company')}
          ${field('Photo URL',     'photo',  'image')}
          ${field('Rating (1-5)',  'rating', 'range', '1,5,1')}
        `,
        'team-member': `
          ${field('Photo', 'photo', 'image')}
          ${field('Name',  'name',  'text', 'Full Name')}
          ${field('Role',  'role',  'text', 'Job Title')}
          ${field('Bio',   'bio',   'textarea')}
        `,
        pricing: `
          ${field('Plan Title',  'title',      'text', 'Pro Plan')}
          ${field('Currency',    'currency',   'text', '$')}
          ${field('Price',       'price',      'text', '29')}
          ${field('Period',      'period',     'text', '/month')}
          ${field('Button Text', 'buttonText', 'text', 'Get Started')}
          ${field('Highlighted', 'highlighted','checkbox')}
          <div class="mb-2">
            <label class="text-xs text-gray-500 block mb-1">Features (one per line)</label>
            <textarea x-model.lazy="featuresText" @input="updateFeatures()" rows="5" class="w-full border dark:border-gray-600 rounded px-2 py-1.5 text-xs dark:bg-gray-800 resize-none" :value="getSelectedWidget().settings.features&&getSelectedWidget().settings.features.join('\\n')"></textarea>
          </div>
        `,
        counter: `
          ${field('End Number', 'end',      'number', 'min=0')}
          ${field('Prefix',     'prefix',   'text',   'e.g. $')}
          ${field('Suffix',     'suffix',   'text',   'e.g. +')}
          ${field('Label',      'label',    'text',   'Happy Clients')}
          ${field('Color',      'color',    'color')}
          ${field('Font Size',  'fontSize', 'range',  '20,80,2')}
        `,
        'progress-bar': `
          ${field('Label',      'label',      'text',  'Skill name')}
          ${field('Percentage', 'percentage', 'range', '0,100,1')}
          ${field('Color',      'color',      'color')}
          ${field('Height (px)','height',     'range', '4,40,2')}
          ${field('Striped',    'striped',    'checkbox')}
        `,
        'circle-progress': `
          ${field('Percentage',  'percentage',  'range', '0,100,1')}
          ${field('Size (px)',   'size',        'range', '60,300,10')}
          ${field('Stroke Width','strokeWidth', 'range', '2,30,2')}
          ${field('Color',       'color',       'color')}
          ${field('Label',       'label',       'text',  'e.g. 75%')}
        `,
        countdown: `
          ${field('Target Date/Time', 'targetDate', 'datetime-local')}
          ${field('Color',           'color',       'color')}
          ${field('Label: Days',     'labelsDay',   'text', 'Days')}
          ${field('Label: Hours',    'labelsHour',  'text', 'Hours')}
          ${field('Label: Min',      'labelsMin',   'text', 'Minutes')}
          ${field('Label: Sec',      'labelsSec',   'text', 'Seconds')}
        `,
        'google-maps': `
          ${field('Address',    'address', 'text',  'New York, NY')}
          ${field('Height (px)','height',  'range', '200,600,20')}
        `,
        'contact-form': `
          ${field('Form Title',      'title',      'text', 'Contact Us')}
          ${field('Submit Button',   'submitText', 'text', 'Send Message')}
          ${field('Success Message', 'successMsg', 'text', 'Thank you!')}
        `,
        'subscribe-form': `
          ${field('Placeholder',  'placeholder', 'text', 'Enter email')}
          ${field('Button Text',  'buttonText',  'text', 'Subscribe')}
        `,
        'search-form': `
          ${field('Placeholder', 'placeholder', 'text', 'Search...')}
          ${field('Button Text', 'buttonText',  'text', 'Search')}
        `,
        'raw-html':  `${field('HTML Code', 'code', 'textarea')}`,
        'post-loop': `
          ${field('Columns',    'columns', 'select', '<option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option>')}
          ${field('Post Count', 'count',   'range',  '1,12,1')}
        `,
        'image-carousel': `
          <div class="mb-2">
            <label class="text-xs text-gray-500 block mb-1">Image URLs (one per line)</label>
            <textarea rows="5" class="w-full border dark:border-gray-600 rounded px-2 py-1.5 text-xs dark:bg-gray-800 resize-none font-mono" :value="getSelectedWidget().settings.images&&getSelectedWidget().settings.images.join('\\n')" @change="getSelectedWidget().settings.images=$event.target.value.split('\\n').filter(u=>u.trim());pushHistory()"></textarea>
          </div>
          ${field('Autoplay', 'autoplay', 'checkbox')}
        `,
        'before-after': `
          ${field('Before Image', 'beforeUrl', 'image')}
          ${field('After Image',  'afterUrl',  'image')}
        `,
        'author-box': `
          ${field('Photo', 'photo', 'image')}
          ${field('Name',  'name',  'text', 'Author')}
          ${field('Bio',   'bio',   'textarea')}
        `,
        'custom-field': `
          ${field('Key',   'fieldKey',   'text', 'field_name')}
          ${field('Value', 'fieldValue', 'text', 'Field value')}
        `,
        accordion: `
          <div class="mb-3">
            <div class="flex items-center justify-between mb-2">
              <label class="text-xs font-bold text-gray-500 uppercase">Items</label>
              <button @click="addAccordionItem()" class="text-xs text-brand-500 hover:text-brand-600 font-medium">+ Add</button>
            </div>
            <div class="space-y-2">
              <template x-for="(item, i) in getSelectedWidget().settings.items" :key="i">
                <div class="border dark:border-gray-700 rounded-lg p-2 space-y-1.5">
                  <input type="text" x-model="item.title" @change="pushHistory()" class="w-full border dark:border-gray-600 rounded px-2 py-1 text-xs dark:bg-gray-800" placeholder="Item title">
                  <textarea x-model="item.content" @change="pushHistory()" rows="2" class="w-full border dark:border-gray-600 rounded px-2 py-1 text-xs dark:bg-gray-800 resize-none" placeholder="Item content..."></textarea>
                  <div class="flex justify-between">
                    <label class="flex items-center gap-1 text-xs"><input type="checkbox" x-model="item.open" @change="pushHistory()"> Open</label>
                    <button @click="getSelectedWidget().settings.items.splice(i,1);pushHistory()" class="text-xs text-red-400 hover:text-red-600">Remove</button>
                  </div>
                </div>
              </template>
            </div>
          </div>
        `,
        tabs: `
          <div class="mb-3">
            <div class="flex items-center justify-between mb-2">
              <label class="text-xs font-bold text-gray-500 uppercase">Tabs</label>
              <button @click="addTabItem()" class="text-xs text-brand-500 hover:text-brand-600 font-medium">+ Add</button>
            </div>
            <div class="space-y-2">
              <template x-for="(item, i) in getSelectedWidget().settings.items" :key="i">
                <div class="border dark:border-gray-700 rounded-lg p-2 space-y-1.5">
                  <input type="text" x-model="item.label" @change="pushHistory()" class="w-full border dark:border-gray-600 rounded px-2 py-1 text-xs dark:bg-gray-800" placeholder="Tab label">
                  <textarea x-model="item.content" @change="pushHistory()" rows="2" class="w-full border dark:border-gray-600 rounded px-2 py-1 text-xs dark:bg-gray-800 resize-none" placeholder="Tab content..."></textarea>
                  <button @click="getSelectedWidget().settings.items.splice(i,1);pushHistory()" class="text-xs text-red-400 hover:text-red-600">Remove</button>
                </div>
              </template>
            </div>
          </div>
        `,
        'icon-list': `
          <div class="mb-3">
            <div class="flex items-center justify-between mb-2">
              <label class="text-xs font-bold text-gray-500 uppercase">Items</label>
              <button @click="getSelectedWidget().settings.items.push({icon:'fas fa-check',text:'New item'});pushHistory()" class="text-xs text-brand-500 font-medium">+ Add</button>
            </div>
            <div class="space-y-2">
              <template x-for="(item, i) in getSelectedWidget().settings.items" :key="i">
                <div class="flex gap-2 items-center">
                  <input x-model="item.icon" @change="pushHistory()" class="w-28 border dark:border-gray-600 rounded px-2 py-1 text-xs dark:bg-gray-800" placeholder="fas fa-check">
                  <input x-model="item.text" @change="pushHistory()" class="flex-1 border dark:border-gray-600 rounded px-2 py-1 text-xs dark:bg-gray-800" placeholder="Item text">
                  <button @click="getSelectedWidget().settings.items.splice(i,1);pushHistory()" class="text-red-400 text-xs">✕</button>
                </div>
              </template>
            </div>
          </div>
          ${field('Icon Color', 'iconColor', 'color')}
          ${field('Icon Size',  'iconSize',  'range',  '10,40,2')}
          ${field('Alignment',  'alignment', 'select', '<option value="left">Left</option><option value="center">Center</option><option value="right">Right</option>')}
        `,
        'alert-box': `
          ${field('Type',        'type',        'select', '<option value="info">Info</option><option value="success">Success</option><option value="warning">Warning</option><option value="error">Error</option>')}
          ${field('Title',       'title',       'text',   'Notice')}
          ${field('Message',     'message',     'textarea')}
          ${field('Dismissible', 'dismissible', 'checkbox')}
          ${field('Show Icon',   'icon',        'checkbox')}
        `,
        'breadcrumbs': `
          <div class="mb-2">
            <div class="flex items-center justify-between mb-1">
              <label class="text-xs text-gray-500 font-bold uppercase">Items</label>
              <button @click="getSelectedWidget().settings.items.push({label:'Page',link:'#'});pushHistory()" class="text-xs text-brand-500">+ Add</button>
            </div>
            <div class="space-y-1.5">
              <template x-for="(item,i) in getSelectedWidget().settings.items" :key="i">
                <div class="flex gap-1 items-center">
                  <input x-model="item.label" @change="pushHistory()" class="flex-1 border dark:border-gray-600 rounded px-2 py-1 text-xs dark:bg-gray-800" placeholder="Label">
                  <input x-model="item.link"  @change="pushHistory()" class="flex-1 border dark:border-gray-600 rounded px-2 py-1 text-xs dark:bg-gray-800" placeholder="Link">
                  <button @click="getSelectedWidget().settings.items.splice(i,1);pushHistory()" class="text-red-400 text-xs">✕</button>
                </div>
              </template>
            </div>
          </div>
          ${field('Separator', 'separator', 'text', '/')}
        `,
        'table': `
          <div class="mb-2">
            <label class="text-xs text-gray-500 font-bold uppercase block mb-1">Headers (comma-separated)</label>
            <input type="text" :value="getSelectedWidget().settings.headers&&getSelectedWidget().settings.headers.join(',')" @change="getSelectedWidget().settings.headers=$event.target.value.split(',').map(h=>h.trim());pushHistory()" class="w-full border dark:border-gray-600 rounded px-2 py-1.5 text-xs dark:bg-gray-800">
          </div>
          <div class="mb-2">
            <label class="text-xs text-gray-500 font-bold uppercase block mb-1">Rows (one per line, cells comma-separated)</label>
            <textarea rows="5" class="w-full border dark:border-gray-600 rounded px-2 py-1.5 text-xs dark:bg-gray-800 font-mono resize-none"
              :value="getSelectedWidget().settings.rows&&getSelectedWidget().settings.rows.map(r=>r.join(',')).join('\\n')"
              @change="getSelectedWidget().settings.rows=$event.target.value.split('\\n').filter(l=>l.trim()).map(l=>l.split(',').map(c=>c.trim()));pushHistory()"></textarea>
          </div>
          ${field('Striped Rows', 'striped',  'checkbox')}
          ${field('Bordered',     'bordered', 'checkbox')}
        `,
        'modal-trigger': `
          ${field('Trigger Button Text', 'triggerText',   'text',  'Open Modal')}
          ${field('Button Color',        'triggerBg',     'color')}
          ${field('Modal Title',         'modalTitle',    'text',  'Modal Title')}
          <div class="mb-2">
            <label class="text-xs text-gray-500 block mb-1">Modal Content (HTML)</label>
            <textarea x-model="getSelectedWidget().settings.modalContent" @change="pushHistory();markDirty()" rows="4" class="w-full border dark:border-gray-600 rounded px-2 py-1.5 text-xs dark:bg-gray-800 font-mono resize-none"></textarea>
          </div>
        `,
        'form-advanced': `
          ${field('Form Title',      'title',      'text', 'Contact Form')}
          ${field('Submit Button',   'submitText', 'text', 'Submit')}
          ${field('Success Message', 'successMsg', 'text', 'Submitted!')}
          <div class="mt-2 p-2 bg-gray-50 dark:bg-gray-800 rounded text-xs text-gray-400">
            <p>Fields: text, email, select, radio, checkbox, textarea, file</p>
            <p class="mt-1">Edit fields in the JS settings or use the built-in template.</p>
          </div>
        `,
      };

      return panels[w.type]
        || `<p class="text-xs text-gray-400 text-center py-4">Settings for "${w.type}" — use the Style tab for customization</p>`;
    },

    // =====================================================================
    // CORE OPERATIONS
    // =====================================================================
    generateId() {
      return 'w_' + Math.random().toString(36).substr(2, 9);
    },

    createWidget(type) {
      return {
        id: this.generateId(),
        type,
        settings: this.getDefaultSettings(type),
        children: ['section', 'container'].includes(type) ? [] : undefined,
      };
    },

    addWidgetToCanvas(type) {
      this.pushHistory();
      const w = this.createWidget(type);
      this.components.push(w);
      this.selectedId = w.id;
      this.markDirty();
      this.$nextTick(() => { this.initSortable(); });
      this.showToast(`${type} added`, 'success');
    },

    selectWidget(id) {
      this.selectedId = id;
      this.rightTab   = 'content';
    },

    selectedWidget() {
      return this.findWidget(this.selectedId, this.components);
    },

    getSelectedWidget() {
      return this.selectedWidget();
    },

    findWidget(id, list) {
      if (!id || !list) return null;
      for (const c of list) {
        if (c.id === id) return c;
        if (c.children) {
          const found = this.findWidget(id, c.children);
          if (found) return found;
        }
        if (c.settings && c.settings.columns) {
          for (const col of c.settings.columns) {
            const found = this.findWidget(id, col);
            if (found) return found;
          }
        }
      }
      return null;
    },

    deleteWidget(id) {
      this.pushHistory();
      this.components = this.removeFromList(id, this.components);
      if (this.selectedId === id) this.selectedId = null;
      this.markDirty();
    },

    removeFromList(id, list) {
      return list.filter(c => {
        if (c.id === id) return false;
        if (c.children) c.children = this.removeFromList(id, c.children);
        return true;
      });
    },

    duplicateWidget(id) {
      this.pushHistory();
      const w = this.findWidget(id, this.components);
      if (!w) return;
      const clone = JSON.parse(JSON.stringify(w));
      clone.id = this.generateId();
      const idx = this.components.findIndex(c => c.id === id);
      if (idx >= 0) this.components.splice(idx + 1, 0, clone);
      else this.components.push(clone);
      this.selectedId = clone.id;
      this.markDirty();
    },

    copyWidget(id) {
      const w = this.findWidget(id, this.components);
      if (w) { this.clipboard = JSON.parse(JSON.stringify(w)); this.showToast('Copied!', 'info'); }
    },

    pasteWidget() {
      if (!this.clipboard) return;
      this.pushHistory();
      const clone = JSON.parse(JSON.stringify(this.clipboard));
      clone.id = this.generateId();
      this.components.push(clone);
      this.selectedId = clone.id;
      this.markDirty();
      this.showToast('Pasted!', 'success');
    },

    moveWidgetUp(id) {
      this.pushHistory();
      const idx = this.components.findIndex(c => c.id === id);
      if (idx > 0) {
        [this.components[idx-1], this.components[idx]] = [this.components[idx], this.components[idx-1]];
        this.markDirty();
      }
    },

    moveWidgetDown(id) {
      this.pushHistory();
      const idx = this.components.findIndex(c => c.id === id);
      if (idx < this.components.length - 1) {
        [this.components[idx], this.components[idx+1]] = [this.components[idx+1], this.components[idx]];
        this.markDirty();
      }
    },

    // =====================================================================
    // DRAG & DROP
    // =====================================================================
    startDragFromLibrary(e, type) {
      this.dragWidget = type;
      e.dataTransfer.setData('text/plain', type);
      e.dataTransfer.effectAllowed = 'copy';
    },

    onCanvasDragOver(e) {
      e.preventDefault();
      e.dataTransfer.dropEffect = 'copy';
    },

    dropOnCanvas(e) {
      e.preventDefault();
      const type = e.dataTransfer.getData('text/plain') || this.dragWidget;
      if (type) { this.addWidgetToCanvas(type); this.dragWidget = null; }
    },

    // =====================================================================
    // UNDO / REDO
    // =====================================================================
    pushHistory() {
      const state = JSON.stringify(this.components);
      if (this.undoStack.length === 0 || this.undoStack[this.undoStack.length-1] !== state) {
        this.undoStack.push(state);
        if (this.undoStack.length > 50) this.undoStack.shift();
        this.redoStack = [];
      }
    },

    undo() {
      if (this.undoStack.length === 0) return;
      this.redoStack.push(JSON.stringify(this.components));
      this.components = JSON.parse(this.undoStack.pop());
      this.markDirty();
    },

    redo() {
      if (this.redoStack.length === 0) return;
      this.undoStack.push(JSON.stringify(this.components));
      this.components = JSON.parse(this.redoStack.pop());
      this.markDirty();
    },

    // =====================================================================
    // CONTEXT MENU
    // =====================================================================
    openContextMenu(e, widgetId) {
      this.contextMenu = { show: true, x: e.clientX, y: e.clientY, widgetId };
      this.selectWidget(widgetId);
    },

    showCanvasContextMenu(e) {
      if (e.target.closest('.canvas-widget')) return;
      this.contextMenu = { show: true, x: e.clientX, y: e.clientY, widgetId: null };
    },

    // =====================================================================
    // MEDIA LIBRARY
    // =====================================================================
    openMediaLibrary(settingsKey) {
      this.selectedMedia = null;
      this.mediaCallback = settingsKey;
      this.showMediaLibrary = true;
    },

    confirmMedia() {
      if (this.selectedMedia && this.mediaCallback) {
        const w = this.selectedWidget();
        if (w) { w.settings[this.mediaCallback] = this.selectedMedia; this.pushHistory(); this.markDirty(); }
      }
      this.showMediaLibrary = false;
    },

    uploadMediaImage(e) {
      const file = e.target.files[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = (ev) => {
        this.mediaImages.push(ev.target.result);
        this.selectedMedia = ev.target.result;
      };
      reader.readAsDataURL(file);
    },

    addMediaUrl() {
      if (this.mediaUrlInput) {
        this.mediaImages.push(this.mediaUrlInput);
        this.selectedMedia = this.mediaUrlInput;
        this.mediaUrlInput = '';
      }
    },

    // =====================================================================
    // AI ASSISTANT
    // =====================================================================
    openAIForWidget() {
      this.aiTargetWidget = this.selectedId;
      this.aiResult = '';
      this.aiPrompt = '';
      this.showAIModal = true;
    },

    async generateAIText() {
      if (!this.aiPrompt) return;
      this.aiLoading = true;
      this.aiResult  = '';
      await new Promise(r => setTimeout(r, 1500));
      const responses = {
        product:     'Experience the future of productivity with our cutting-edge solution. Designed for professionals who demand excellence, our platform combines intuitive design with powerful features to help you achieve more in less time.',
        hero:        "Transform Your Digital Presence\n\nDiscover a smarter way to build, grow, and scale your online business. Join thousands of satisfied customers who've already made the switch.",
        testimonial: '"This product has completely transformed how our team works. The intuitive interface and powerful features have saved us countless hours every week. Highly recommended!" - Sarah Johnson, Marketing Director',
        cta:         'Ready to take your business to the next level? Join our growing community of successful entrepreneurs and start your journey today. Limited spots available — act now and get your first month free!',
      };
      const key = Object.keys(responses).find(k => this.aiPrompt.toLowerCase().includes(k)) || 'product';
      this.aiResult  = responses[key];
      this.aiLoading = false;
    },

    insertAIText() {
      const w = this.findWidget(this.aiTargetWidget, this.components);
      if (w) {
        if      (w.type === 'heading')     w.settings.text    = this.aiResult;
        else if (w.type === 'paragraph')   w.settings.content = `<p>${this.aiResult}</p>`;
        else if (w.type === 'testimonial') w.settings.text    = this.aiResult;
        else if (w.settings.text    !== undefined) w.settings.text    = this.aiResult;
        else if (w.settings.content !== undefined) w.settings.content = `<p>${this.aiResult}</p>`;
        this.pushHistory();
        this.markDirty();
        this.showToast('AI text inserted!', 'success');
      }
      this.showAIModal = false;
    },

    // =====================================================================
    // TEMPLATES
    // =====================================================================
    saveTemplate() {
      if (!this.newTemplateName.trim()) { this.showToast('Enter a template name', 'warning'); return; }
      this.templates.push({ name: this.newTemplateName, date: Date.now(), components: JSON.parse(JSON.stringify(this.components)), globalStyles: JSON.parse(JSON.stringify(this.globalStyles)) });
      this.saveTemplatesToStorage();
      this.newTemplateName = '';
      this.showToast('Template saved!', 'success');
    },

    loadTemplate(i) {
      this.pushHistory();
      const tpl = this.templates[i];
      this.components   = JSON.parse(JSON.stringify(tpl.components));
      this.globalStyles = { ...this.globalStyles, ...tpl.globalStyles };
      this.showTemplatesModal = false;
      this.markDirty();
      this.showToast('Template loaded!', 'success');
    },

    deleteTemplate(i) {
      this.templates.splice(i, 1);
      this.saveTemplatesToStorage();
      this.showToast('Template deleted', 'info');
    },

    loadTemplates()         { try { this.templates = JSON.parse(localStorage.getItem('cms_templates') || '[]'); } catch(e) {} },
    saveTemplatesToStorage(){ localStorage.setItem('cms_templates', JSON.stringify(this.templates)); },

    // =====================================================================
    // REVISIONS
    // =====================================================================
    saveRevision(label) {
      this.revisions.unshift({ label: label || 'Snapshot', date: Date.now(), components: JSON.parse(JSON.stringify(this.components)) });
      if (this.revisions.length > 20) this.revisions.pop();
      this.saveRevisionsToStorage();
    },

    restoreRevision(i) {
      this.pushHistory();
      this.components = JSON.parse(JSON.stringify(this.revisions[i].components));
      this.showRevisionsModal = false;
      this.markDirty();
      this.showToast('Revision restored!', 'success');
    },

    loadRevisions()         { try { this.revisions = JSON.parse(localStorage.getItem('cms_revisions') || '[]'); } catch(e) {} },
    saveRevisionsToStorage(){ localStorage.setItem('cms_revisions', JSON.stringify(this.revisions)); },

    // =====================================================================
    // SAVE / LOAD
    // =====================================================================
    savePage(status) {
      const isHeader = this.builderMode === 'header';
      const data = { components: this.components, globalStyles: this.globalStyles, seoData: this.seoData, savedAt: Date.now() };
      localStorage.setItem('cms_page_data', JSON.stringify(data));
      this.isDirty = false;
      this.autoSaveIndicator = true;
      setTimeout(() => this.autoSaveIndicator = false, 3000);
      this.showToast(isHeader ? 'Header saved!' : 'Page saved!', 'success');

      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const pageId    = document.querySelector('meta[name="page-id"]')?.getAttribute('content')    || null;
      const url       = isHeader ? `{{ route("admin.headers.store") }}` : pageId ? `/admin/pages/${pageId}` : `{{ route("admin.pages.store") }}`;
      const method    = isHeader ? 'POST' : (pageId ? 'PUT' : 'POST');
      const body      = isHeader ? JSON.stringify({
        name:       this.headerName || 'Untitled Header',
        is_default: this.isDefault ? 1 : 0,
        content:    { widgets: this.components.map(c => ({ type: c.type, settings: c.settings })), settings: this.pageSettings || {}, globalStyles: this.globalStyles }
      }) : JSON.stringify({
        title:            this.seoData.title || 'Untitled Page',
        slug:             (this.seoData.title || 'untitled').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, ''),
        content:          JSON.stringify(data),
        status:           status || 'draft',
        meta_title:       this.seoData.title,
        meta_description: this.seoData.meta,
      });

      fetch(url, {
        method: method,
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: body
      })
      .then(res => res.json())
      .then(res => {
        if (res.success && (res.header_id || res.page_id)) {
          if (res.redirect) window.location.href = res.redirect;
        }
      })
      .catch(err => console.warn('Server save error:', err));
    },

    loadFromStorage() {
      try {
        // Skip localStorage for header create mode to avoid stale page builder state
        if (typeof _HEADER_CREATE_MODE !== 'undefined' && _HEADER_CREATE_MODE) {
          return;
        }
        const raw = localStorage.getItem('cms_page_data');
        if (raw) {
          const data = JSON.parse(raw);
          this.components   = data.components   || [];
          if (data.globalStyles) this.globalStyles = { ...this.globalStyles, ...data.globalStyles };
          if (data.seoData)      this.seoData      = { ...this.seoData,      ...data.seoData };
        }
      } catch(e) {}
    },

    autoSave() {
      if (this.isDirty) {
        this.savePage();
        this.autoSaveIndicator = true;
        setTimeout(() => this.autoSaveIndicator = false, 2000);
      }
    },

    publishPage() {
      this.savePage('published');
      this.showToast(this.builderMode === 'header' ? 'Header published!' : 'Page published!', 'success');
    },
    markDirty()   { this.isDirty = true; },

    // =====================================================================
    // EXPORT / IMPORT
    // =====================================================================
    exportJSON() {
      const data = { version: '1.0', components: this.components, globalStyles: this.globalStyles, seoData: this.seoData, exportedAt: new Date().toISOString() };
      const blob  = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
      const url   = URL.createObjectURL(blob);
      const a     = document.createElement('a');
      a.href = url; a.download = 'page.json'; a.click();
      URL.revokeObjectURL(url);
      this.showToast('Page exported as page.json', 'success');
    },

    importJSON(e) {
      const file = e.target.files[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = (ev) => {
        try {
          const data = JSON.parse(ev.target.result);
          this.pushHistory();
          this.components = data.components || [];
          if (data.globalStyles) this.globalStyles = { ...this.globalStyles, ...data.globalStyles };
          if (data.seoData)      this.seoData      = { ...this.seoData,      ...data.seoData };
          this.markDirty();
          this.showToast('Page imported successfully!', 'success');
        } catch (err) { this.showToast('Invalid JSON file', 'error'); }
      };
      reader.readAsText(file);
      e.target.value = '';
    },

    // =====================================================================
    // KEYBOARD SHORTCUTS
    // =====================================================================
    handleKeydown(e) {
      const ctrl    = e.ctrlKey || e.metaKey;
      const inField = e.target.matches('input,textarea,select,[contenteditable]');

      if (e.key === '?')                              { this.showShortcutsModal = !this.showShortcutsModal; return; }
      if (ctrl && e.key === 's')                      { e.preventDefault(); this.savePage(); return; }
      if (ctrl && e.key === 'z')                      { e.preventDefault(); this.undo(); return; }
      if (ctrl && e.key === 'y')                      { e.preventDefault(); this.redo(); return; }
      if (ctrl && e.key === 'c' && this.selectedId)   { e.preventDefault(); this.copyWidget(this.selectedId); return; }
      if (ctrl && e.key === 'v')                      { e.preventDefault(); this.pasteWidget(); return; }
      if (ctrl && e.key === 'd' && this.selectedId)   { e.preventDefault(); this.duplicateWidget(this.selectedId); return; }
      if (ctrl && e.key === 'p')                      { e.preventDefault(); this.livePreview = !this.livePreview; return; }
      if (ctrl && e.key === 'g')                      { e.preventDefault(); this.snapGrid = !this.snapGrid; return; }

      if (!inField && this.selectedId) {
        if (e.key === 'ArrowUp'   && e.altKey)  { e.preventDefault(); this.moveWidgetUp(this.selectedId); return; }
        if (e.key === 'ArrowDown' && e.altKey)  { e.preventDefault(); this.moveWidgetDown(this.selectedId); return; }
        if (e.key === 'ArrowUp')   { e.preventDefault(); const idx = this.components.findIndex(c=>c.id===this.selectedId); if (idx > 0) this.selectWidget(this.components[idx-1].id); return; }
        if (e.key === 'ArrowDown') { e.preventDefault(); const idx = this.components.findIndex(c=>c.id===this.selectedId); if (idx < this.components.length-1) this.selectWidget(this.components[idx+1].id); return; }
        if (e.key === 'Escape')    { this.selectedId = null; return; }
      }
      if ((e.key === 'Delete' || e.key === 'Backspace') && this.selectedId && !inField) {
        e.preventDefault(); this.deleteWidget(this.selectedId);
      }
    },

    handleBeforeUnload(e) {
      if (this.isDirty) { e.preventDefault(); e.returnValue = 'You have unsaved changes. Are you sure you want to leave?'; }
    },

    // =====================================================================
    // DARK MODE
    // =====================================================================
    toggleDarkMode() {
      this.darkMode = !this.darkMode;
      localStorage.setItem('builder_dark', this.darkMode);
    },

    // =====================================================================
    // CANVAS STYLES
    // =====================================================================
    canvasContainerStyle() {
      const widths = { desktop: '100%', tablet: '800px', mobile: '420px' };
      return `max-width:${widths[this.previewMode]||'100%'};`;
    },

    getPageBgStyle() {
      let style = `background-color:${this.globalStyles.bgColor};`;
      if (this.globalStyles.bgImage) {
        style += `background-image:url(${this.globalStyles.bgImage});background-size:${this.globalStyles.bgSize};background-position:center;`;
      }
      return style;
    },

    // =====================================================================
    // SEO HELPERS
    // =====================================================================
    countImages()   { return this.components.filter(c => c.type === 'image' || c.type === 'image-carousel').length; },
    countHeadings() { return this.components.filter(c => c.type === 'heading').length; },

    // =====================================================================
    // ACCESSIBILITY CHECK
    // =====================================================================
    runA11yCheck() {
      const issues = [];
      let id = 0;
      this.components.forEach(c => {
        if (c.type === 'image' && !c.settings.alt)
          issues.push({id:id++, level:'error', title:'Image missing alt text', desc:`Widget "${c.settings.label||c.id}" has no alt attribute. Screen readers won't describe it.`});
        if (c.type === 'heading' && c.settings.tag === 'h1') {
          const h1count = this.components.filter(x=>x.type==='heading'&&x.settings.tag==='h1').length;
          if (h1count > 1) issues.push({id:id++, level:'error', title:'Multiple H1 headings', desc:'Page should have only one H1 tag for SEO and accessibility.'});
        }
        if (c.type === 'button' && !c.settings.text)
          issues.push({id:id++, level:'error', title:'Button has no text', desc:'Buttons need descriptive text for keyboard and screen reader users.'});
        if (c.type === 'heading' && c.settings.color === '#ffffff' && !c.settings.bgColor)
          issues.push({id:id++, level:'warning', title:'Possible low contrast', desc:`Heading "${c.settings.text}" uses white text — check background contrast ratio.`});
        if (c.type === 'video')
          issues.push({id:id++, level:'warning', title:'Video: add captions', desc:'Ensure embedded videos have closed captions for hearing-impaired users.'});
      });
      if (!this.components.some(c=>c.type==='heading'&&c.settings.tag==='h1') && this.components.length > 0)
        issues.push({id:id++, level:'warning', title:'No H1 heading found', desc:'Add an H1 heading to clearly define the page topic for SEO and screen readers.'});
      if (!this.seoData.title || this.seoData.title.length < 10)
        issues.push({id:id++, level:'warning', title:'SEO title too short', desc:'Page title should be at least 10 characters for good SEO.'});
      if (!this.seoData.meta)
        issues.push({id:id++, level:'warning', title:'Missing meta description', desc:'Add a meta description (100-160 chars) for better search engine snippets.'});
      this.a11yIssues = issues;
    },

    // =====================================================================
    // PAGE VERSIONS
    // =====================================================================
    saveVersion() {
      if (!this.newVersionName.trim()) { this.showToast('Enter a version name', 'warning'); return; }
      this.pageVersions.unshift({ name: this.newVersionName, date: Date.now(), components: JSON.parse(JSON.stringify(this.components)), globalStyles: JSON.parse(JSON.stringify(this.globalStyles)) });
      if (this.pageVersions.length > 10) this.pageVersions.pop();
      this.saveVersionsToStorage();
      this.newVersionName = '';
      this.showToast('Version saved!', 'success');
    },

    loadVersion(i) {
      this.pushHistory();
      this.components   = JSON.parse(JSON.stringify(this.pageVersions[i].components));
      this.globalStyles = { ...this.globalStyles, ...this.pageVersions[i].globalStyles };
      this.showVersionsModal = false;
      this.markDirty();
      this.showToast('Version loaded!', 'success');
      // Reopen modal to show updated active version after load
      this.$nextTick(() => {
        setTimeout(() => this.showVersionsModal = true, 100);
      });
    },

    loadVersions()         { try { this.pageVersions = JSON.parse(localStorage.getItem('cms_versions') || '[]'); } catch(e) {} },
    saveVersionsToStorage(){ localStorage.setItem('cms_versions', JSON.stringify(this.pageVersions)); },

    // =====================================================================
    // WIDGET LIBRARY FILTER
    // =====================================================================
    filteredWidgetCategories() {
      if (!this.widgetSearch) return this.widgetCategories;
      const q = this.widgetSearch.toLowerCase();
      return this.widgetCategories
        .map(cat => ({ ...cat, open: true, widgets: cat.widgets.filter(w => w.label.toLowerCase().includes(q) || w.type.includes(q)) }))
        .filter(cat => cat.widgets.length > 0);
    },

    // =====================================================================
    // WIDGET ICON HELPER
    // =====================================================================
    getWidgetIcon(type) {
      const icons = {
        section:'fa-square', container:'fa-box', columns:'fa-columns', spacer:'fa-arrows-alt-v',
        divider:'fa-minus', heading:'fa-heading', paragraph:'fa-paragraph', button:'fa-hand-pointer',
        image:'fa-image', video:'fa-video', icon:'fa-star', 'icon-list':'fa-list-ul',
        testimonial:'fa-quote-right', 'team-member':'fa-user-tie', pricing:'fa-tag',
        accordion:'fa-layer-group', tabs:'fa-folder', counter:'fa-sort-numeric-up',
        'progress-bar':'fa-tasks', 'circle-progress':'fa-circle-notch', countdown:'fa-clock',
        'image-carousel':'fa-images', 'before-after':'fa-adjust', lottie:'fa-film',
        'google-maps':'fa-map-marker-alt', 'post-loop':'fa-rss', 'post-meta':'fa-info',
        'author-box':'fa-user', 'custom-field':'fa-database', 'contact-form':'fa-envelope',
        'subscribe-form':'fa-bell', 'search-form':'fa-search', 'raw-html':'fa-code',
        'alert-box':'fa-exclamation-circle', 'breadcrumbs':'fa-chevron-right',
        'table':'fa-table', 'modal-trigger':'fa-window-restore', 'form-advanced':'fa-wpforms',
      };
      return icons[type] || 'fa-puzzle-piece';
    },

    // =====================================================================
    // WIDGET-SPECIFIC HELPERS
    // =====================================================================
    addAccordionItem() {
      const w = this.selectedWidget();
      if (w && w.settings.items) { w.settings.items.push({ title: 'New Section', content: '<p>New content</p>', open: false }); this.pushHistory(); }
    },

    addTabItem() {
      const w = this.selectedWidget();
      if (w && w.settings.items) { w.settings.items.push({ label: 'New Tab', content: '<p>Tab content</p>' }); this.pushHistory(); }
    },

    updateFeatures() {
      const w = this.selectedWidget();
      if (w && this.featuresText !== undefined) { w.settings.features = this.featuresText.split('\n').filter(f => f.trim()); this.pushHistory(); }
    },

    // =====================================================================
    // TOAST NOTIFICATIONS
    // =====================================================================
    showToast(message, type = 'info') {
      const id = Date.now();
      this.toasts.push({ id, message, type });
      setTimeout(() => this.removeToast(id), 4000);
    },

    removeToast(id) {
      this.toasts = this.toasts.filter(t => t.id !== id);
    },

  }; // end return
}
</script>