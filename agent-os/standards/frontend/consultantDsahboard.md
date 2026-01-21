import React, { useState, useEffect } from 'react';
import { 
  Trophy, 
  Coins, 
  Zap, 
  Briefcase, 
  Gamepad2, 
  Star, 
  Rocket, 
  Clock, 
  ChevronRight,
  ChevronLeft,
  Search,
  Bell,
  MoreVertical,
  Download,
  FileText,
  Plus,
  Flame,
  Target,
  Dribbble,
  MousePointer2,
  CheckCircle2,
  ArrowUpRight,
  Compass,
  LayoutGrid,
  Layers,
  ShoppingBag,
  Monitor,
  Gamepad,
  Smartphone,
  Gift,
  Palette,
  Play,
  Info,
  Menu,
  X
} from 'lucide-react';

// --- CUSTOM SVG ICONS ---
const DSDollarIcon = () => (
  <div className="w-6 h-6 bg-gradient-to-tr from-yellow-400 to-orange-500 rounded-full flex items-center justify-center shadow-[0_0_10px_rgba(234,179,8,0.5)] border-2 border-white/50 shrink-0">
    <span className="text-[10px] font-black text-white italic">DS</span>
  </div>
);

const USFlagIcon = () => (
  <div className="w-6 h-6 rounded-md overflow-hidden flex items-center justify-center border border-gray-200 shrink-0">
    <svg viewBox="0 0 741 390" className="w-full h-full">
      <path fill="#bd3d44" d="M0 0h741v390H0z"/>
      <path d="M0 30h741v30H0m0 60h741v30H0m0 60h741v30H0m0 60h741v30H0m0 60h741v30H0m0 60h741v30H0" fill="#fff"/>
      <path fill="#192f5d" d="M0 0h296.4v210H0z"/>
    </svg>
  </div>
);

// --- MOCK DATA ---
const PROJECTS = [
  {
    id: 1,
    client: 'Stellar VR',
    title: 'Metaverse Brand Identity',
    status: 'In Progress',
    deadline: '2 days',
    reward: '2,400',
    xp: '+450 XP',
    health: 85,
    img: 'https://images.unsplash.com/photo-1614850523296-d8c1af93d400?auto=format&fit=crop&q=80&w=800',
    tags: ['Branding', '3D'],
    brief: 'Create a cohesive visual identity for a VR social platform focused on architectural visualization.',
    nextTask: 'Finalize typography for the spatial UI elements.'
  },
  {
    id: 2,
    client: 'Bloom Coffee',
    title: 'Packaging Suite v2',
    status: 'Revision',
    deadline: '24 hours',
    reward: '1,200',
    xp: '+200 XP',
    health: 40,
    img: 'https://images.unsplash.com/photo-1559056199-641a0ac8b55e?auto=format&fit=crop&q=80&w=800',
    tags: ['Packaging', 'Print'],
    brief: 'Redesign the eco-friendly bean pouches with a focus on minimalist illustrative patterns.',
    nextTask: 'Apply the copper-foil overlay to the medium roast labels.'
  },
  {
    id: 3,
    client: 'CyberNet',
    title: 'E-Sports Portal UI',
    status: 'Drafting',
    deadline: '5 days',
    reward: '3,800',
    xp: '+900 XP',
    health: 15,
    img: 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?auto=format&fit=crop&q=80&w=800',
    tags: ['UI/UX', 'Gaming'],
    brief: 'Develop a high-intensity dashboard for semi-pro tournament organizers to manage player stats.',
    nextTask: 'Design the responsive layout for the match-bracket viewer.'
  }
];

const DAILY_QUESTS = [
  { id: 1, text: 'Submit the "Final_Final" logo for FoxTrot', xp: 50, completed: true },
  { id: 2, text: 'Critique 2 community moodboards', xp: 20, completed: false },
  { id: 3, text: 'Upload source files for Project Solaris', xp: 100, completed: false },
];

const REWARDS_STORE = [
  { id: 1, name: 'Holographic "Drift" Sticker Pack', price: '150', category: 'Physical', icon: Palette, color: 'text-pink-400' },
  { id: 2, name: 'Abstract Desktop Wallpaper Bundle', price: '50', category: 'Digital', icon: Monitor, color: 'text-blue-400' },
  { id: 3, name: '$20 Steam / PSN Voucher', price: '800', category: 'Coupon', icon: Gamepad, color: 'text-indigo-400' },
  { id: 4, name: '10GB Airtime Bundle (All Networks)', price: '450', category: 'Utilities', icon: Smartphone, color: 'text-green-400' },
  { id: 5, name: 'NVIDIA RTX 4080 Super (Waitlist)', price: '45,000', category: 'Hardware', icon: Zap, color: 'text-yellow-400' },
  { id: 6, name: 'PlayStation 5 Console (Disc Edition)', price: '25,000', category: 'Hardware', icon: Gamepad2, color: 'text-white' },
];

// --- COMPONENTS ---

const NavItem = ({ icon: Icon, label, active, onClick, badge, isCollapsed }) => (
  <button 
    onClick={onClick}
    title={isCollapsed ? label : ""}
    className={`w-full flex items-center justify-between px-4 py-3 rounded-2xl transition-all duration-300 group relative ${
      active 
        ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-lg shadow-indigo-200' 
        : 'text-gray-500 hover:bg-white hover:text-black'
    }`}
  >
    <div className="flex items-center space-x-3 overflow-hidden">
      <Icon size={20} className={`shrink-0 ${active ? 'text-white' : 'group-hover:scale-110 transition-transform'}`} />
      {!isCollapsed && (
        <span className="font-bold text-sm tracking-tight whitespace-nowrap opacity-100 transition-opacity duration-300">
          {label}
        </span>
      )}
    </div>
    {!isCollapsed && badge && (
      <span className={`text-[10px] px-2 py-0.5 rounded-full font-black shrink-0 ${active ? 'bg-white/20 text-white' : 'bg-indigo-100 text-indigo-600'}`}>
        {badge}
      </span>
    )}
    {isCollapsed && badge && (
      <div className="absolute top-1 right-1 w-2 h-2 bg-indigo-600 rounded-full border-2 border-white" />
    )}
  </button>
);

const StatCard = ({ label, value, icon, color, currencyIcon: Currency }) => (
  <div className="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
    <div className="flex items-center justify-between mb-4">
      <div className={`p-3 rounded-2xl ${color} bg-opacity-10 text-opacity-100`}>
        {icon}
      </div>
      <div className="flex items-center space-x-1.5 bg-gray-50 px-3 py-1 rounded-full border border-gray-100">
        <Currency />
        <span className="text-[10px] font-black uppercase text-gray-400 tracking-widest">{label}</span>
      </div>
    </div>
    <div className="flex items-baseline space-x-1">
      <span className="text-3xl font-black tracking-tighter">{value}</span>
    </div>
  </div>
);

const ProjectCard = ({ project }) => {
  const [isFlipped, setIsFlipped] = useState(false);

  return (
    <div 
      className="[perspective:1000px] h-[520px] cursor-pointer group"
      onMouseEnter={() => setIsFlipped(true)}
      onMouseLeave={() => setIsFlipped(false)}
      onClick={() => setIsFlipped(!isFlipped)}
    >
      <div 
        className={`relative w-full h-full transition-transform duration-700 [transform-style:preserve-3d] ${isFlipped ? '[transform:rotateY(180deg)]' : ''}`}
      >
        {/* FRONT SIDE */}
        <div 
          className="absolute inset-0 [backface-visibility:hidden] bg-white rounded-[2.5rem] border border-gray-100 shadow-sm flex flex-col overflow-hidden"
        >
          <div className="relative h-56 overflow-hidden">
            <img src={project.img} className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000" alt={project.title} />
            <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-60" />
            
            <div className="absolute top-4 left-4 flex gap-2">
              {project.tags.map(tag => (
                <span key={tag} className="bg-white/10 backdrop-blur-md text-white text-[9px] font-black uppercase tracking-widest px-3 py-1.5 rounded-full border border-white/20">
                  {tag}
                </span>
              ))}
            </div>

            <div className="absolute bottom-6 left-6 right-6">
              <p className="text-indigo-400 text-[10px] font-black uppercase tracking-widest mb-1">{project.client}</p>
              <h3 className="text-white text-xl font-black leading-tight tracking-tight">{project.title}</h3>
            </div>
          </div>

          <div className="p-8 flex-grow flex flex-col space-y-6">
            <div className="space-y-2">
              <div className="flex justify-between items-center text-[10px] font-black uppercase text-gray-400 tracking-[0.2em]">
                <span>Project Health</span>
                <span className={project.health > 70 ? 'text-green-500' : project.health > 30 ? 'text-orange-500' : 'text-red-500'}>
                  {project.health}%
                </span>
              </div>
              <div className="h-2.5 w-full bg-gray-100 rounded-full overflow-hidden">
                <div 
                  className={`h-full transition-all duration-1000 ${project.health > 70 ? 'bg-green-500' : project.health > 30 ? 'bg-orange-500' : 'bg-red-500'}`} 
                  style={{ width: `${project.health}%` }} 
                />
              </div>
            </div>

            <div className="grid grid-cols-2 gap-4">
              <div className="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Bounty</p>
                <div className="flex items-center space-x-1">
                   <span className="text-lg font-black tracking-tighter">${project.reward}</span>
                </div>
              </div>
              <div className="bg-indigo-50/50 p-4 rounded-2xl border border-indigo-100/50">
                <p className="text-[9px] font-black text-indigo-400 uppercase tracking-widest mb-1">Quest Timer</p>
                <div className="flex items-center space-x-1 text-indigo-700">
                  <Clock size={14} className="animate-pulse" />
                  <span className="text-[11px] font-black uppercase tracking-tight">{project.deadline} left</span>
                </div>
              </div>
            </div>

            <div className="mt-auto pt-6 border-t border-gray-50 flex items-center justify-between">
              <span className="text-[10px] font-bold text-gray-400 italic">Tap or hover for brief...</span>
              <div className="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400">
                <Info size={14} />
              </div>
            </div>
          </div>
        </div>

        {/* BACK SIDE */}
        <div 
          className="absolute inset-0 [backface-visibility:hidden] [transform:rotateY(180deg)] bg-gray-950 rounded-[2.5rem] border border-white/10 shadow-2xl p-8 flex flex-col text-white"
        >
          <div className="flex items-center space-x-3 mb-8">
            <div className="p-3 bg-indigo-600 rounded-2xl shadow-lg shadow-indigo-900/40">
              <FileText size={20} />
            </div>
            <div>
              <p className="text-[10px] font-black text-indigo-400 uppercase tracking-widest">Project Brief</p>
              <h3 className="text-lg font-black tracking-tight">{project.client} Strategy</h3>
            </div>
          </div>

          <div className="flex-grow space-y-8">
            <div className="space-y-2">
              <p className="text-[10px] font-black text-white/30 uppercase tracking-[0.2em]">Objective</p>
              <p className="text-sm font-medium leading-relaxed text-gray-300">
                {project.brief}
              </p>
            </div>

            <div className="p-5 bg-white/5 rounded-2xl border border-white/5 space-y-3">
              <div className="flex items-center space-x-2">
                <Zap size={14} className="text-yellow-400 fill-current" />
                <p className="text-[10px] font-black text-white/50 uppercase tracking-widest">Next Quest Step</p>
              </div>
              <p className="text-xs font-bold leading-snug">
                {project.nextTask}
              </p>
            </div>
          </div>

          <div className="mt-auto pt-8 border-t border-white/10">
            <button 
              className="w-full py-4 bg-indigo-600 hover:bg-indigo-500 text-white rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] flex items-center justify-center space-x-3 transition-all shadow-xl shadow-indigo-950"
              onClick={(e) => {
                e.stopPropagation();
                // Handle start working logic
              }}
            >
              <Play size={16} className="fill-current" />
              <span>Start Working Session</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};

// --- MAIN APP ---

export default function App() {
  const [activeTab, setActiveTab] = useState('Workshop');
  const [isLoaded, setIsLoaded] = useState(false);
  const [isCollapsed, setIsCollapsed] = useState(false);
  const [isMobileOpen, setIsMobileOpen] = useState(false);

  useEffect(() => {
    const link = document.createElement('link');
    link.href = 'https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap';
    link.rel = 'stylesheet';
    document.head.appendChild(link);
    setTimeout(() => setIsLoaded(true), 100);
    return () => document.head.removeChild(link);
  }, []);

  return (
    <div 
      className={`flex min-h-screen bg-[#FDFDFF] text-[#1A1A1A] transition-opacity duration-1000 ${isLoaded ? 'opacity-100' : 'opacity-0'}`} 
      style={{ fontFamily: 'Montserrat, sans-serif' }}
    >
      {/* MOBILE OVERLAY */}
      {isMobileOpen && (
        <div 
          className="fixed inset-0 bg-black/50 z-[60] lg:hidden backdrop-blur-sm transition-opacity"
          onClick={() => setIsMobileOpen(false)}
        />
      )}

      {/* SIDEBAR */}
      <aside 
        className={`
          fixed lg:sticky top-0 h-screen z-[70] bg-white border-r border-gray-100 flex flex-col p-6 transition-all duration-300 ease-in-out
          ${isMobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'}
          ${isCollapsed ? 'w-24' : 'w-80'}
        `}
      >
        {/* SIDEBAR TOGGLE BUTTON (Desktop) */}
        <button 
          onClick={() => setIsCollapsed(!isCollapsed)}
          className="absolute -right-3 top-28 bg-white border border-gray-100 rounded-full p-1.5 shadow-sm hidden lg:flex text-gray-400 hover:text-black z-10 transition-transform"
        >
          {isCollapsed ? <ChevronRight size={14} /> : <ChevronLeft size={14} />}
        </button>

        {/* LOGO */}
        <div className={`mb-12 flex items-center ${isCollapsed ? 'justify-center' : 'space-x-3'}`}>
          <div className="w-12 h-12 bg-indigo-600 rounded-[1.25rem] flex items-center justify-center shadow-xl shadow-indigo-200 rotate-3 hover:rotate-0 transition-transform cursor-pointer shrink-0">
            <Zap size={24} className="text-white fill-current" />
          </div>
          {!isCollapsed && (
            <div className="flex flex-col whitespace-nowrap overflow-hidden transition-all duration-300">
              <span className="text-2xl font-black leading-none tracking-tighter">DRIFT</span>
              <span className="text-[10px] font-black tracking-[0.4em] text-indigo-400 -mt-0.5">CONSULTANT</span>
            </div>
          )}
        </div>

        {/* NAVIGATION */}
        <nav className="flex-grow space-y-3 overflow-y-auto no-scrollbar">
          <p className={`px-4 text-[10px] font-black text-gray-300 uppercase tracking-[0.2em] mb-4 transition-all ${isCollapsed ? 'text-center' : ''}`}>
            {isCollapsed ? "•••" : "The Workroom"}
          </p>
          <NavItem isCollapsed={isCollapsed} icon={LayoutGrid} label="The Workshop" active={activeTab === 'Workshop'} onClick={() => setActiveTab('Workshop')} badge="3" />
          <NavItem isCollapsed={isCollapsed} icon={Target} label="Daily Quests" active={activeTab === 'Quests'} onClick={() => setActiveTab('Quests')} />
          <NavItem isCollapsed={isCollapsed} icon={Layers} label="Project Vault" active={activeTab === 'Vault'} onClick={() => setActiveTab('Vault')} />
          <NavItem isCollapsed={isCollapsed} icon={Trophy} label="Leaderboard" active={activeTab === 'Board'} onClick={() => setActiveTab('Board')} />
          
          <div className="pt-8">
            <p className={`px-4 text-[10px] font-black text-gray-300 uppercase tracking-[0.2em] mb-4 transition-all ${isCollapsed ? 'text-center' : ''}`}>
              {isCollapsed ? "•••" : "Earning"}
            </p>
            <NavItem isCollapsed={isCollapsed} icon={ShoppingBag} label="Loot Store" active={activeTab === 'Drops'} onClick={() => setActiveTab('Drops')} badge="HOT" />
            <NavItem isCollapsed={isCollapsed} icon={Gamepad2} label="Skill Challenges" active={activeTab === 'Skills'} onClick={() => setActiveTab('Skills')} />
          </div>
        </nav>

        {/* SIDEBAR FOOTER */}
        <div className="mt-auto pt-8 border-t border-gray-100">
          {!isCollapsed ? (
            <div className="bg-indigo-50 rounded-3xl p-6 mb-6">
              <div className="flex justify-between items-center mb-2">
                <span className="text-[10px] font-black text-indigo-400 uppercase tracking-widest">Level 24</span>
                <span className="text-[10px] font-black text-indigo-600">80%</span>
              </div>
              <div className="h-2 w-full bg-white rounded-full overflow-hidden mb-4">
                <div className="h-full bg-indigo-600 w-4/5" />
              </div>
              <p className="text-[11px] font-bold text-indigo-900 leading-tight">Master Pixel Wrangler</p>
            </div>
          ) : (
            <div className="flex flex-col items-center mb-6">
               <div className="w-10 h-10 rounded-full border-4 border-indigo-100 flex items-center justify-center relative">
                  <div className="w-6 h-6 rounded-full border-4 border-indigo-600 border-t-transparent animate-spin absolute" />
                  <span className="text-[10px] font-black">24</span>
               </div>
            </div>
          )}
          <button className={`w-full flex items-center space-x-3 px-4 py-3 rounded-2xl text-gray-400 hover:text-black hover:bg-gray-50 transition-all font-bold text-sm ${isCollapsed ? 'justify-center' : ''}`}>
            <Compass size={20} className="shrink-0" />
            {!isCollapsed && <span>Community</span>}
          </button>
        </div>
      </aside>

      {/* MAIN CONTENT AREA */}
      <main className="flex-grow min-w-0">
        {/* HEADER */}
        <div className="h-24 bg-white/80 backdrop-blur-md border-b border-gray-100 px-6 lg:px-12 flex items-center justify-between sticky top-0 z-50">
          <div className="flex items-center space-x-4">
            {/* MOBILE TOGGLE */}
            <button 
              onClick={() => setIsMobileOpen(true)}
              className="lg:hidden p-2 text-gray-500 hover:text-black"
            >
              <Menu size={24} />
            </button>
            <div className="hidden md:flex items-center bg-gray-50 px-6 py-3 rounded-2xl border border-gray-100 w-64 lg:w-96 group focus-within:ring-2 focus-within:ring-indigo-600 transition-all">
              <Search size={18} className="text-gray-400 group-hover:text-indigo-600 transition-colors" />
              <input type="text" placeholder="Find project files..." className="bg-transparent border-none focus:ring-0 text-sm font-bold w-full ml-3 placeholder:text-gray-300" />
            </div>
          </div>

          <div className="flex items-center space-x-4 lg:space-x-8">
            <div className="hidden sm:flex space-x-2">
              <button className="p-3 bg-white border border-gray-100 rounded-2xl hover:bg-gray-50 transition-colors text-gray-500"><Bell size={20} /></button>
              <button className="p-3 bg-white border border-gray-100 rounded-2xl hover:bg-gray-50 transition-colors text-gray-500"><Plus size={20} /></button>
            </div>
            
            <div className="flex items-center space-x-4 lg:pl-8 lg:border-l border-gray-100">
              <div className="text-right hidden sm:block">
                <p className="text-sm font-black tracking-tight">Alex Rivera</p>
                <p className="text-[10px] font-black text-indigo-400 uppercase tracking-widest">Senior Designer</p>
              </div>
              <div className="w-12 h-12 rounded-3xl bg-indigo-100 border-2 border-white shadow-lg overflow-hidden flex items-center justify-center shrink-0">
                 <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Alex" alt="Avatar" />
              </div>
            </div>
          </div>
        </div>

        <div className="p-6 lg:p-12 max-w-[1400px] mx-auto space-y-12">
          {/* CURRENCY & STATS */}
          <section className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 animate-in fade-in slide-in-from-top-4 duration-700">
            <StatCard label="USD Balance" value="12,450.00" icon={<Briefcase size={24} />} color="text-green-600 bg-green-500" currencyIcon={USFlagIcon} />
            <StatCard label="DS Dollars" value="4,820" icon={<Coins size={24} />} color="text-yellow-600 bg-yellow-500" currencyIcon={DSDollarIcon} />
            <StatCard label="Current XP" value="82,044" icon={<Star size={24} />} color="text-indigo-600 bg-indigo-500" currencyIcon={() => <Zap size={14} className="text-indigo-600" />} />
            <StatCard label="Global Rank" value="#142" icon={<Trophy size={24} />} color="text-purple-600 bg-purple-500" currencyIcon={() => <Rocket size={14} className="text-purple-600" />} />
          </section>

          {/* TOP SECTION: QUESTS & LOOT STACKED */}
          <div className="flex flex-col gap-8">
              {/* DAILY QUESTS */}
              <div className="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm animate-in fade-in slide-in-from-left-4 duration-1000 delay-200 flex flex-col">
                <div className="flex items-center justify-between mb-8">
                  <div className="flex items-center space-x-3">
                    <div className="p-3 bg-indigo-50 text-indigo-600 rounded-2xl">
                      <Target size={24} />
                    </div>
                    <div>
                      <h3 className="font-black tracking-tight text-xl italic uppercase">Daily Quests</h3>
                      <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">In-game Objectives</p>
                    </div>
                  </div>
                  <div className="flex items-center space-x-3">
                    <span className="text-xs font-black text-indigo-500 bg-indigo-50 px-3 py-1 rounded-full uppercase hidden sm:inline-block">2/3 DONE</span>
                    <button className="text-[10px] font-black text-gray-300 hover:text-indigo-600 uppercase tracking-widest transition-colors">Refresh in 14h</button>
                  </div>
                </div>
                
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                  {DAILY_QUESTS.map(quest => (
                    <div key={quest.id} className="flex items-center justify-between p-5 bg-gray-50 rounded-[2rem] group cursor-pointer hover:bg-indigo-50 transition-colors border border-transparent hover:border-indigo-100">
                      <div className="flex items-center space-x-4">
                        <div className={`w-8 h-8 rounded-xl border-2 flex items-center justify-center transition-all ${quest.completed ? 'bg-indigo-600 border-indigo-600 text-white' : 'bg-white border-gray-200 text-transparent group-hover:border-indigo-400'}`}>
                          <CheckCircle2 size={16} />
                        </div>
                        <p className={`text-sm font-bold tracking-tight ${quest.completed ? 'text-gray-400 line-through' : 'text-gray-700'}`}>{quest.text}</p>
                      </div>
                      <span className="text-[10px] font-black text-indigo-400 italic shrink-0">+{quest.xp} XP</span>
                    </div>
                  ))}
                </div>
              </div>

              {/* REWARDS LOOT STORE */}
              <div className="bg-gray-950 p-8 rounded-[2.5rem] text-white shadow-2xl relative overflow-hidden animate-in fade-in slide-in-from-right-4 duration-1000 delay-300 flex flex-col">
                <div className="absolute top-0 right-0 p-8 opacity-10">
                  <ShoppingBag size={120} />
                </div>
                <div className="relative z-10">
                  <div className="flex items-center justify-between mb-8">
                    <div className="flex items-center space-x-3">
                      <div className="p-3 bg-white/10 rounded-2xl text-yellow-400">
                        <Coins size={24} />
                      </div>
                      <div>
                        <h3 className="font-black tracking-tight text-xl italic uppercase">The Loot Store</h3>
                        <p className="text-[10px] font-bold text-white/30 uppercase tracking-widest">DS Dollar Rewards</p>
                      </div>
                    </div>
                    <button className="text-[10px] font-black bg-indigo-600 px-4 py-2 rounded-full hover:bg-indigo-500 transition-all uppercase tracking-[0.2em] shadow-lg shadow-indigo-900/40">Open Full Vault</button>
                  </div>
                  
                  <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    {REWARDS_STORE.map(item => (
                      <div key={item.id} className="group cursor-pointer bg-white/5 border border-white/5 p-5 rounded-[2rem] hover:bg-white/10 transition-all hover:border-white/10">
                        <div className="flex items-center justify-between mb-4">
                          <div className={`p-3 bg-white/5 rounded-2xl ${item.color} border border-white/5`}>
                            <item.icon size={20} />
                          </div>
                          <p className="text-sm font-black text-yellow-400 italic">{item.price} DS</p>
                        </div>
                        <div className="mb-4">
                          <p className="text-xs font-black text-white group-hover:text-indigo-400 transition-colors tracking-tight line-clamp-1">{item.name}</p>
                          <p className="text-[9px] font-bold text-white/30 tracking-[0.2em] uppercase">{item.category}</p>
                        </div>
                        <button className="w-full py-2.5 bg-white text-black rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-gray-200 transition-all">
                          Claim
                        </button>
                      </div>
                    ))}
                  </div>
                </div>
              </div>
          </div>

          {/* LOWER SECTION: THE WORKSHOP */}
          <section className="space-y-10 animate-in fade-in slide-in-from-bottom-6 duration-1000 delay-500">
            <div className="flex flex-col sm:flex-row sm:items-center justify-between border-b-2 border-gray-50 pb-6 gap-6">
              <div>
                <h2 className="text-3xl lg:text-4xl font-black tracking-tighter italic uppercase text-gray-900">The Workshop</h2>
                <p className="text-gray-400 font-medium mt-1">Manage your active creative quests.</p>
              </div>
              <div className="flex bg-gray-100 p-1.5 rounded-2xl border border-gray-200 w-fit">
                <button className="bg-white text-black px-4 lg:px-6 py-3 rounded-xl text-[10px] lg:text-xs font-black shadow-sm uppercase tracking-widest transition-all">Active Sessions</button>
                <button className="text-gray-400 px-4 lg:px-6 py-3 rounded-xl text-[10px] lg:text-xs font-black uppercase tracking-widest hover:text-gray-600 transition-colors">Paused</button>
              </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-10">
              {PROJECTS.map(project => (
                <ProjectCard key={project.id} project={project} />
              ))}
              
              <div className="bg-gray-50 border-2 border-dashed border-gray-200 rounded-[2.5rem] flex flex-col items-center justify-center p-12 text-center group cursor-pointer hover:border-indigo-400 transition-colors h-[520px]">
                <div className="w-20 h-20 bg-white rounded-3xl flex items-center justify-center text-gray-300 mb-6 group-hover:scale-110 group-hover:bg-indigo-50 group-hover:text-indigo-500 transition-all shadow-sm border border-gray-100">
                  <Plus size={40} />
                </div>
                <h3 className="font-black text-gray-400 group-hover:text-indigo-600 transition-colors uppercase tracking-[0.2em] text-sm">Pick Up a Quest</h3>
                <p className="text-xs font-bold text-gray-300 mt-3 max-w-[200px] leading-relaxed">Browse the market for new high-bounty design opportunities.</p>
              </div>
            </div>
          </section>

          <footer className="mt-20 pt-16 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center text-center md:text-left gap-6 pb-12 shrink-0">
            <div>
              <p className="text-black font-black text-sm uppercase tracking-widest mb-1">DRIFT WORKSHOP V3.0</p>
              <p className="text-gray-400 text-xs font-bold uppercase tracking-widest">Design Consultant Portal • Enterprise Grid</p>
            </div>
            <div className="flex flex-wrap justify-center md:justify-end gap-x-10 gap-y-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">
              <a href="#" className="hover:text-indigo-600 transition-colors">The Vault</a>
              <a href="#" className="hover:text-indigo-600 transition-colors">Support</a>
              <a href="#" className="hover:text-indigo-600 transition-colors">Legal</a>
            </div>
          </footer>
        </div>
      </main>
    </div>
  );
}