import React, { useState, useEffect, useMemo } from 'react';
import { 
  LayoutDashboard, 
  FolderOpen, 
  Sparkles, 
  User, 
  Settings, 
  LogOut, 
  Calendar, 
  CheckCircle2, 
  Clock, 
  ChevronRight,
  TrendingUp, 
  CreditCard, 
  Search, 
  Bell, 
  MoreVertical, 
  ExternalLink, 
  Files, 
  BrainCircuit, 
  ListChecks, 
  Printer, 
  ArrowRight, 
  Map, 
  PlayCircle, 
  Download, 
  FileText, 
  Image as ImageIcon, 
  Archive, 
  RotateCw, 
  Eye,
  Menu,
  X
} from 'lucide-react';

// --- DATA DEFINITIONS ---

const JOURNEY_STAGES = [
  { 
    id: 1, 
    phase: 1, 
    title: 'Logo Design', 
    progress: 100, 
    consultant: 'FoxTrot', 
    initials: 'FT', 
    start: '01 Nov 2023', 
    end: '15 Nov 2023', 
    img: 'https://images.unsplash.com/photo-1626785774573-4b799315345d?auto=format&fit=crop&q=80&w=800',
    files: [
      { name: 'Primary_Logo_FullColor.svg', type: 'vector', size: '1.2MB' },
      { name: 'Logo_Mark_Black.png', type: 'image', size: '850KB' },
      { name: 'Logo_Mark_White.png', type: 'image', size: '840KB' },
      { name: 'Logo_Vertical_Stacked.pdf', type: 'pdf', size: '2.4MB' },
      { name: 'Brand_Favicon_Set.zip', type: 'archive', size: '4.1MB' }
    ]
  },
  { 
    id: 2, 
    phase: 1, 
    title: 'Brand Manual', 
    progress: 100, 
    consultant: 'DeltaLima', 
    initials: 'DL', 
    start: '16 Nov 2023', 
    end: '30 Nov 2023', 
    img: 'https://images.unsplash.com/photo-1586717791821-3f44a563dc4c?auto=format&fit=crop&q=80&w=800',
    files: [
      { name: 'Zimbabwe_Tourism_BrandBook_v1.pdf', type: 'pdf', size: '12.5MB' },
      { name: 'Typography_Guidelines.pdf', type: 'pdf', size: '1.1MB' },
      { name: 'Color_Palette_Specs.ase', type: 'vector', size: '12KB' }
    ]
  },
  { 
    id: 3, 
    phase: 1, 
    title: 'Digital Strategy', 
    progress: 65, 
    consultant: 'SierraTang', 
    initials: 'ST', 
    start: '01 Dec 2023', 
    end: '10 Dec 2023', 
    img: 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&q=80&w=800',
    tasks: ['Review Competitor Analysis', 'Approve Content Pillars', 'Schedule Launch Call']
  },
  { id: 4, phase: 1, title: 'Stationery Kit', progress: 0, consultant: 'BravoEcho', initials: 'BE', start: '11 Dec 2023', end: '20 Dec 2023', img: 'https://images.unsplash.com/photo-1583508915901-b5f84c1dcde1?auto=format&fit=crop&q=80&w=800', tasks: ['Complete Design Questionnaire', 'Upload Business Card Details'] },
  { id: 5, phase: 1, title: 'Website & Domain', progress: 0, consultant: 'AlphaZulu', initials: 'AZ', start: '05 Jan 2024', end: '25 Jan 2024', img: 'https://images.unsplash.com/photo-1547658719-da2b51169166?auto=format&fit=crop&q=80&w=800', tasks: ['Select Domain Name', 'Verify Hosting Access'] },
  { id: 6, phase: 1, title: 'Branded Collateral', progress: 0, consultant: 'MikeOscar', initials: 'MO', start: '01 Feb 2024', end: '15 Feb 2024', img: 'https://images.unsplash.com/photo-1542744094-24638eff58bb?auto=format&fit=crop&q=80&w=800', tasks: ['Finalize Asset List', 'Send Project Brief'] },
  { id: 7, phase: 2, title: 'Apparel & Merch', progress: 0, consultant: 'KiloRomeo', initials: 'KR', start: '01 Mar 2024', end: '15 Mar 2024', img: 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&q=80&w=800', tasks: ['Size Charts Confirmation', 'Fabric Selection'] },
  { id: 8, phase: 2, title: 'Events & Activations', progress: 0, consultant: 'PapaUniform', initials: 'PU', start: '20 Mar 2024', end: '10 Apr 2024', img: 'https://images.unsplash.com/photo-1505373877841-8d25f7d46678?auto=format&fit=crop&q=80&w=800', tasks: ['Event Date Lock', 'Vendor Coordination'] },
  { id: 9, phase: 2, title: 'Vehicle & Spatial', progress: 0, consultant: 'CharlieVictor', initials: 'CV', start: '15 Apr 2024', end: '30 Apr 2024', img: 'https://images.unsplash.com/photo-1517048676732-d65bc937f952?auto=format&fit=crop&q=80&w=800', tasks: ['Vehicle Dimensions Upload', 'Shop Front Photos'] },
  { id: 10, phase: 2, title: 'Content & Video', progress: 0, consultant: 'HotelXray', initials: 'HX', start: '01 May 2024', end: '20 May 2024', img: 'https://images.unsplash.com/photo-1492724441997-5dc865305da7?auto=format&fit=crop&q=80&w=800', tasks: ['Storyboard Review', 'Voiceover Selection'] },
  { id: 11, phase: 2, title: 'Analytics Setup', progress: 0, consultant: 'TangoYankee', initials: 'TY', start: '25 May 2024', end: '05 Jun 2024', img: 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&q=80&w=800', tasks: ['Pixel Installation', 'Tag Manager Access'] },
  { id: 12, phase: 2, title: 'Campaigns', progress: 0, consultant: 'JulietGolf', initials: 'JG', start: '10 Jun 2024', end: '30 Jun 2024', img: 'https://images.unsplash.com/photo-1557838923-2985c318be48?auto=format&fit=crop&q=80&w=800', tasks: ['Budget Allocation', 'Targeting Approval'] },
];

// --- COMPONENTS ---

const SidebarItem = ({ icon: Icon, label, active, onClick }) => (
  <button 
    onClick={onClick}
    className={`w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-300 group ${
      active 
        ? 'bg-black text-white shadow-xl' 
        : 'text-gray-500 hover:bg-gray-100 hover:text-black'
    }`}
  >
    <Icon size={20} className={active ? 'text-white' : 'group-hover:scale-110 transition-transform'} />
    <span className="font-semibold text-sm">{label}</span>
  </button>
);

const FileIcon = ({ type }) => {
  switch (type) {
    case 'pdf': return <FileText size={20} className="text-red-500" />;
    case 'image': return <ImageIcon size={20} className="text-blue-500" />;
    case 'vector': return <ImageIcon size={20} className="text-orange-500" />;
    case 'archive': return <Archive size={20} className="text-amber-600" />;
    default: return <Files size={20} className="text-gray-400" />;
  }
};

const JourneyCard = ({ stage }) => {
  const [isFlipped, setIsFlipped] = useState(false);
  const isComplete = stage.progress === 100;
  const isStarted = stage.progress > 0;

  const handleToggle = (e) => {
    setIsFlipped(!isFlipped);
  };

  return (
    <div 
      className="[perspective:1000px] h-[480px] cursor-pointer group"
      onClick={handleToggle}
    >
      <div 
        className={`relative w-full h-full transition-transform duration-700 [transform-style:preserve-3d] ${isFlipped ? '[transform:rotateY(180deg)]' : 'lg:group-hover:[transform:rotateY(180deg)]'}`}
      >
        {/* FRONT SIDE */}
        <div 
          className={`absolute inset-0 [backface-visibility:hidden] bg-white rounded-3xl border border-gray-100 shadow-sm flex flex-col overflow-hidden ${!isStarted ? 'bg-gray-50/50' : ''}`}
        >
          <div className="relative h-56 overflow-hidden">
            <div 
              className={`absolute inset-0 bg-cover bg-center transition-all duration-1000 ease-in-out group-hover:scale-110 ${!isStarted ? 'grayscale opacity-40' : ''}`}
              style={{ backgroundImage: `url(${stage.img})` }}
            />
            <div className={`absolute inset-0 bg-gradient-to-t from-black via-black/30 to-transparent ${!isStarted ? 'from-gray-900/80' : ''}`} />
            
            <div className="absolute top-5 left-5 flex flex-wrap gap-2">
              <span className="bg-white/10 backdrop-blur-md text-white text-[10px] uppercase tracking-widest px-3 py-1.5 rounded-full font-bold border border-white/20">
                Phase {stage.phase}
              </span>
              {isComplete && (
                <span className="bg-green-500 text-white px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-wider flex items-center space-x-1 shadow-lg">
                  <CheckCircle2 size={10} />
                  <span>Complete</span>
                </span>
              )}
            </div>

            <div className="absolute bottom-6 left-6 right-6">
              <p className="text-white/60 text-[10px] font-black uppercase tracking-[0.2em] mb-1">Stage {stage.id.toString().padStart(2, '0')}</p>
              <h3 className="text-white font-extrabold text-2xl leading-none tracking-tight">{stage.title}</h3>
            </div>
          </div>

          <div className="p-6 flex-grow flex flex-col justify-between">
            <div className="space-y-6">
              <div className="space-y-2">
                <div className="flex justify-between items-center text-[10px] font-black uppercase tracking-widest">
                  <span className="text-gray-400">Status</span>
                  <span className={isComplete ? 'text-green-500' : isStarted ? 'text-blue-600' : 'text-gray-300'}>
                    {isComplete ? '100% DONE' : isStarted ? `${stage.progress}% IN PROGRESS` : 'NOT STARTED'}
                  </span>
                </div>
                <div className="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                  <div 
                    className={`h-full transition-all duration-1000 ease-out rounded-full ${isComplete ? 'bg-green-500' : isStarted ? 'bg-black' : 'bg-gray-300'}`}
                    style={{ width: `${stage.progress}%` }}
                  />
                </div>
              </div>

              <div className="space-y-1">
                <p className="text-[10px] text-gray-400 font-black uppercase tracking-widest">Lead Consultant</p>
                <div className="flex items-center space-x-2">
                  <div className={`w-8 h-8 rounded-xl flex items-center justify-center font-black text-xs border ${!isStarted ? 'bg-gray-50 text-gray-400 border-gray-100' : 'bg-gray-100 text-black border-gray-200'}`}>
                    {stage.initials}
                  </div>
                  <span className={`text-xs font-bold ${!isStarted ? 'text-gray-400' : 'text-gray-800'}`}>{stage.consultant}</span>
                </div>
              </div>
            </div>

            <div className="mt-auto pt-6 border-t border-gray-50">
              <div className="flex items-center justify-between">
                <div className="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">
                  <span>{isComplete ? 'Finished' : 'Est. Finish'}</span>
                  <p className="text-gray-900 leading-tight mt-1">{stage.end}</p>
                </div>
                <div className="text-blue-500 animate-pulse hidden lg:block">
                   <RotateCw size={16} />
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* BACK SIDE */}
        <div 
          className="absolute inset-0 [backface-visibility:hidden] [transform:rotateY(180deg)] bg-gray-950 rounded-3xl border border-white/10 shadow-2xl p-8 flex flex-col text-white"
        >
          <div className="flex items-center justify-between mb-8">
            <div>
              <p className="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-1">Phase {stage.phase} Details</p>
              <h3 className="text-2xl font-black tracking-tight">{stage.title}</h3>
            </div>
            <div className="w-10 h-10 bg-white/10 rounded-2xl flex items-center justify-center border border-white/10">
              {isComplete ? <Files size={18} /> : <ListChecks size={18} />}
            </div>
          </div>

          <div className="flex-grow overflow-y-auto custom-scrollbar pr-2">
            {isComplete ? (
              <div className="space-y-3">
                <p className="text-[10px] font-black text-white/40 uppercase tracking-[0.2em] mb-4">Completed Deliverables</p>
                {stage.files?.map((file, idx) => (
                  <a 
                    key={idx} 
                    href="#" 
                    className="flex items-center justify-between p-3 bg-white/5 rounded-2xl border border-white/5 hover:bg-white/10 hover:border-white/20 transition-all duration-300"
                    onClick={(e) => e.stopPropagation()}
                  >
                    <div className="flex items-center space-x-3 overflow-hidden">
                      <div className="p-2 bg-white/5 rounded-xl">
                        <FileIcon type={file.type} />
                      </div>
                      <div className="overflow-hidden">
                        <p className="text-[10px] font-black text-white truncate leading-none mb-1">{file.name}</p>
                        <p className="text-[9px] font-bold text-white/30 uppercase tracking-tighter">{file.size}</p>
                      </div>
                    </div>
                    <Download size={14} className="text-white/40" />
                  </a>
                ))}
              </div>
            ) : (
              <div className="space-y-6">
                <p className="text-[10px] font-black text-white/40 uppercase tracking-[0.2em] mb-4">Strategic Requirements</p>
                <div className="space-y-4">
                  {(stage.tasks || ['Define stage objectives', 'Consultation briefing', 'Asset gathering']).map((task, idx) => (
                    <div key={idx} className="flex items-start space-x-3 group">
                      <div className="mt-1 w-4 h-4 rounded-md border border-white/20 flex items-center justify-center shrink-0">
                        <div className="w-1.5 h-1.5 bg-blue-500 rounded-sm opacity-0 group-hover:opacity-100 transition-opacity" />
                      </div>
                      <p className="text-[11px] font-bold text-white/70 group-hover:text-white transition-colors">{task}</p>
                    </div>
                  ))}
                </div>
              </div>
            )}
          </div>

          {/* FLIP SIDE FOOTER BUTTONS */}
          <div className="mt-8 pt-6 border-t border-white/10 space-y-3">
            {isComplete && (
              <button 
                className="w-full py-3 rounded-xl border-2 border-white/20 text-white font-black text-[10px] uppercase tracking-[0.2em] flex items-center justify-center space-x-2 hover:bg-white/10 transition-all"
                onClick={(e) => e.stopPropagation()}
              >
                <Eye size={14} />
                <span>View Files</span>
              </button>
            )}
            
            <button 
              className={`w-full py-4 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] flex items-center justify-center space-x-2 transition-all shadow-xl ${isComplete ? 'bg-blue-600 hover:bg-blue-500' : 'bg-white text-black hover:bg-gray-200'}`}
              onClick={(e) => e.stopPropagation()}
            >
              {isComplete ? (
                <>
                  <Download size={14} />
                  <span>Download Full Kit</span>
                </>
              ) : (
                <>
                  <ChevronRight size={14} />
                  <span>Stage Workroom</span>
                </>
              )}
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};

// --- MAIN APP ---

export default function App() {
  const [activeTab, setActiveTab] = useState('Dashboard');
  const [isLoaded, setIsLoaded] = useState(false);
  const [isSidebarOpen, setIsSidebarOpen] = useState(false);

  useEffect(() => {
    const link = document.createElement('link');
    link.href = 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap';
    link.rel = 'stylesheet';
    document.head.appendChild(link);
    setTimeout(() => setIsLoaded(true), 100);
    return () => document.head.removeChild(link);
  }, []);

  const currentStage = useMemo(() => {
    return JOURNEY_STAGES.find(s => s.progress > 0 && s.progress < 100) || JOURNEY_STAGES[0];
  }, []);

  return (
    <div 
      className={`flex min-h-screen bg-[#FAFAFA] text-[#1A1A1A] transition-opacity duration-1000 ${isLoaded ? 'opacity-100' : 'opacity-0'}`} 
      style={{ fontFamily: 'Inter, sans-serif' }}
    >
      {/* Mobile Sidebar Overlay */}
      {isSidebarOpen && (
        <div 
          className="fixed inset-0 bg-black/50 z-40 lg:hidden backdrop-blur-sm transition-all duration-300"
          onClick={() => setIsSidebarOpen(false)}
        />
      )}

      <aside 
        className={`fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-gray-100 flex flex-col p-8 transition-transform duration-300 ease-in-out lg:translate-x-0 lg:sticky lg:top-0 lg:h-screen lg:flex ${
          isSidebarOpen ? 'translate-x-0' : '-translate-x-full'
        }`}
      >
        <div className="mb-12 flex items-center justify-between">
          <div className="flex items-center space-x-3">
            <div className="w-10 h-10 bg-black rounded-2xl flex items-center justify-center shadow-2xl">
              <div className="w-3 h-3 bg-white rounded-full animate-pulse" />
            </div>
            <div className="flex flex-col">
              <span className="text-xl font-black leading-none tracking-tighter">DRIFT</span>
              <span className="text-[10px] font-bold tracking-[0.4em] text-gray-400 -mt-0.5">STUDIO</span>
            </div>
          </div>
          <button 
            className="lg:hidden p-2 -mr-2 text-gray-400 hover:text-black hover:bg-gray-100 rounded-lg transition-all"
            onClick={() => setIsSidebarOpen(false)}
          >
            <X size={20} />
          </button>
        </div>

        <nav className="flex-grow space-y-2">
          <p className="px-4 text-[10px] font-black text-gray-300 uppercase tracking-[0.2em] mb-4">Core Portal</p>
          <SidebarItem icon={LayoutDashboard} label="Dashboard" active={activeTab === 'Dashboard'} onClick={() => { setActiveTab('Dashboard'); setIsSidebarOpen(false); }} />
          <SidebarItem icon={Map} label="The Journey" active={activeTab === 'Journey'} onClick={() => { setActiveTab('Journey'); setIsSidebarOpen(false); }} />
          <SidebarItem icon={FolderOpen} label="Design Files" active={activeTab === 'Files'} onClick={() => { setActiveTab('Files'); setIsSidebarOpen(false); }} />
          <SidebarItem icon={Sparkles} label="Bespoke Request" active={activeTab === 'Bespoke Request'} onClick={() => { setActiveTab('Bespoke Request'); setIsSidebarOpen(false); }} />
        </nav>

        <div className="mt-auto pt-8 border-t border-gray-100 space-y-2">
          <SidebarItem icon={User} label="Settings & Profile" active={activeTab === 'Profile'} onClick={() => { setActiveTab('Profile'); setIsSidebarOpen(false); }} />
          <button className="w-full flex items-center space-x-3 px-4 py-3 rounded-xl text-red-500 hover:bg-red-50 transition-all font-bold text-sm">
            <LogOut size={20} />
            <span>Log out</span>
          </button>
        </div>
      </aside>

      <main className="flex-grow w-full lg:w-auto">
        <div className="h-20 bg-white/80 backdrop-blur-md border-b border-gray-100 px-6 lg:px-12 flex items-center justify-between sticky top-0 z-40">
          <div className="flex items-center">
            <button 
              className="mr-6 lg:hidden p-2 -ml-2 text-gray-500 hover:text-black hover:bg-gray-100 rounded-lg transition-all"
              onClick={() => setIsSidebarOpen(true)}
            >
              <Menu size={24} />
            </button>
            <div className="hidden md:flex items-center bg-gray-50 px-4 py-2 rounded-full border border-gray-100 w-96 group focus-within:ring-2 focus-within:ring-black transition-all">
              <Search size={18} className="text-gray-400 group-hover:text-black transition-colors" />
              <input type="text" placeholder="Search roadmap items..." className="bg-transparent border-none focus:ring-0 text-sm font-medium w-full ml-2 placeholder:text-gray-300 outline-none" />
            </div>
          </div>

          <div className="flex items-center space-x-4 lg:space-x-6 ml-auto md:ml-0">
            <button className="relative p-2 text-gray-400 hover:text-black transition-colors">
              <Bell size={22} />
              <span className="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white" />
            </button>
            <div className="flex items-center space-x-3 pl-6 border-l border-gray-100">
              <div className="text-right hidden sm:block">
                <p className="text-xs font-black">Zimbabwe Tourism</p>
                <p className="text-[10px] font-bold text-gray-400">Enterprise Account</p>
              </div>
              <div className="w-10 h-10 rounded-full bg-black flex items-center justify-center text-white font-black text-xs">ZT</div>
            </div>
          </div>
        </div>

        <div className="p-6 lg:p-12 max-w-7xl mx-auto">
          {activeTab === 'Dashboard' ? (
            <div className="space-y-12 animate-in fade-in slide-in-from-bottom-4 duration-1000">
              <header className="mb-12">
                <div className="flex items-center space-x-2 text-blue-600 mb-2">
                  <Sparkles size={16} />
                  <span className="text-[10px] font-black uppercase tracking-[0.3em]">Design-as-a-Service Dashboard</span>
                </div>
                <h1 className="text-4xl lg:text-5xl font-black tracking-tight leading-none mb-4">Welcome back.</h1>
                <p className="text-gray-400 font-medium">Your brand evolution is currently on track.</p>
              </header>

              <div className="space-y-12">
                <div className="bg-black rounded-[2.5rem] shadow-2xl overflow-hidden h-[500px] flex flex-col group relative">
                  <div className="absolute inset-0 z-0">
                    <img 
                      src={currentStage.img} 
                      alt={currentStage.title}
                      className="w-full h-full object-cover opacity-60 group-hover:scale-105 transition-transform duration-[4000ms] ease-out"
                    />
                    <div className="absolute inset-0 bg-gradient-to-t from-black via-black/40 to-transparent" />
                  </div>
                  <div className="relative z-10 p-10 lg:p-16 flex flex-col h-full justify-between text-white">
                    <div className="max-w-2xl">
                      <div className="inline-flex items-center space-x-2 bg-white/10 backdrop-blur-md px-4 py-2 rounded-full border border-white/20 mb-8">
                        <Clock size={16} className="text-blue-400" />
                        <span className="text-[11px] font-black uppercase tracking-[0.2em]">Active Current Stage</span>
                      </div>
                      <h2 className="text-5xl lg:text-7xl font-black tracking-tighter leading-none mb-4">{currentStage.title}</h2>
                      <p className="text-white/60 text-lg font-medium">Lead Strategist: <span className="text-white font-bold">{currentStage.consultant}</span></p>
                    </div>
                    <div className="mt-auto max-w-3xl space-y-8">
                      <div className="space-y-4">
                        <div className="flex items-center justify-between text-[11px] font-black uppercase tracking-[0.2em] text-white/40">
                          <span>Milestone Progress</span>
                          <span className="text-blue-400">{currentStage.progress}%</span>
                        </div>
                        <div className="h-2 w-full bg-white/10 rounded-full overflow-hidden">
                          <div className="h-full bg-blue-500 rounded-full" style={{ width: `${currentStage.progress}%` }} />
                        </div>
                      </div>
                      <div className="flex flex-wrap gap-4">
                        <button className="flex items-center justify-center space-x-3 bg-white text-black px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-gray-200 transition-all hover:scale-105">
                          <PlayCircle size={20} />
                          <span>Resume Work Session</span>
                        </button>
                        <button className="flex items-center justify-center space-x-3 bg-white/10 backdrop-blur-md border border-white/20 px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-white/20 transition-all">
                          <Printer size={20} />
                          <span>Print Center</span>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <div className="bg-white rounded-[2.5rem] border border-gray-100 shadow-xl overflow-hidden grid grid-cols-1 lg:grid-cols-12 min-h-[400px]">
                  <div className="lg:col-span-7 p-8 lg:p-12 bg-gradient-to-br from-white to-gray-50/50">
                    <div className="flex items-center space-x-3 mb-6 text-blue-600">
                      <BrainCircuit size={24} />
                      <span className="text-[11px] font-black uppercase tracking-[0.3em]">Drift AI Business Analysis</span>
                    </div>
                    <h2 className="text-3xl font-black tracking-tight mb-6">Strategic Trajectory</h2>
                    <p className="text-gray-600 font-medium leading-relaxed">
                      Identity enterprise-ready. Current focus on <span className="text-blue-600 font-bold">Digital Strategy</span> indicates pivot to market positioning. Brand health: <span className="text-green-500 font-bold">88%</span>.
                    </p>
                  </div>
                  <div className="lg:col-span-5 p-8 lg:p-12 bg-gray-50 border-l border-gray-100">
                    <div className="flex items-center space-x-3 text-blue-600 mb-8">
                      <ListChecks size={20} />
                      <span className="text-[11px] font-black uppercase tracking-[0.3em]">Growth Recommendations</span>
                    </div>
                    <div className="space-y-6 text-xs font-bold text-gray-700">
                      <div className="flex items-center space-x-3 group cursor-pointer">
                        <div className="w-5 h-5 rounded-md border-2 border-gray-200 group-hover:border-blue-400" />
                        <p>Share digital flyer with 10 network leads</p>
                      </div>
                      <div className="flex items-center space-x-3 group cursor-pointer">
                        <div className="w-5 h-5 rounded-md border-2 border-gray-200 group-hover:border-blue-400" />
                        <p>Complete consultant questionnaire</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          ) : activeTab === 'Journey' ? (
            <div className="space-y-24 animate-in fade-in duration-1000">
              <header className="mb-16">
                <div className="flex items-center space-x-2 text-blue-600 mb-2">
                  <Map size={16} />
                  <span className="text-[10px] font-black uppercase tracking-[0.3em]">Full Roadmap Access</span>
                </div>
                <h1 className="text-4xl lg:text-6xl font-black tracking-tight leading-none">The Journey.</h1>
                <p className="text-gray-400 font-medium mt-4">Systematic brand communication roadmap for exponential business growth.</p>
              </header>

              <section>
                <div className="flex items-end justify-between mb-10 border-b-2 border-black/5 pb-4">
                  <div className="space-y-1">
                    <h2 className="text-[10px] font-black text-gray-300 uppercase tracking-[0.5em]">Foundational</h2>
                    <h3 className="text-3xl font-black tracking-tight uppercase">Phase One</h3>
                  </div>
                </div>
                <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-10 items-start">
                  {JOURNEY_STAGES.filter(s => s.phase === 1).map(stage => (
                    <JourneyCard key={stage.id} stage={stage} />
                  ))}
                </div>
              </section>

              <section>
                <div className="flex items-end justify-between mb-10 border-b-2 border-black/5 pb-4">
                  <div className="space-y-1">
                    <h2 className="text-[10px] font-black text-gray-300 uppercase tracking-[0.5em]">Awareness & Sales</h2>
                    <h3 className="text-3xl font-black tracking-tight uppercase">Phase Two</h3>
                  </div>
                </div>
                <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-10 items-start">
                  {JOURNEY_STAGES.filter(s => s.phase === 2).map(stage => (
                    <JourneyCard key={stage.id} stage={stage} />
                  ))}
                </div>
              </section>
            </div>
          ) : (
            <div className="flex flex-col items-center justify-center py-20 text-center">
              <div className="w-20 h-20 bg-gray-100 rounded-3xl flex items-center justify-center text-gray-300 mb-6"><Clock size={40} /></div>
              <h2 className="text-2xl font-black">{activeTab} Section</h2>
              <p className="text-gray-400 mt-2">Optimizing module for your enterprise account.</p>
            </div>
          )}

          <footer className="mt-32 pt-16 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center text-center md:text-left gap-6 pb-12 shrink-0">
            <div>
              <p className="text-black font-black text-sm uppercase tracking-widest mb-1">Drift Journey System v3.0</p>
              <p className="text-gray-400 text-xs font-bold uppercase tracking-widest">Enterprise Design Infrastructure</p>
            </div>
            <div className="flex space-x-8 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
              <a href="#" className="hover:text-black transition-colors">Terms</a>
              <a href="#" className="hover:text-black transition-colors">Privacy</a>
              <a href="#" className="hover:text-black transition-colors">Support</a>
            </div>
          </footer>
        </div>
      </main>
    </div>
  );
}