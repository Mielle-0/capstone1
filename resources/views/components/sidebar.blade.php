<!-- Updated layout styles -->
<style>
body { padding-top: 56px; }
.sidebar {
    width: 260px;
    position: fixed;
    top: 56px;
    left: 0;
    height: calc(100vh - 56px);
    background: #f8f9fa;
    border-right: 1px solid #dee2e6;
    overflow-y: auto;
    overflow-x: hidden;
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE and Edge */
}
.sidebar::-webkit-scrollbar {
    display: none; /* Chrome, Safari, Opera */
}
.main-content {
    margin-left: 260px;
    padding: 20px;
}
.section-header {
    cursor: pointer;
    user-select: none; /* Prevents text highlighting when double-clicked */
}
.collapse-icon {
    transition: transform 0.3s ease;
}
/* When collapsed, rotate the arrow to point right */
.section.collapsed .collapse-icon {
    transform: rotate(-90deg); 
}
</style>

<!-- resources/views/components/sidebar.blade.php -->
<div class="sidebar bg-white border-end">
    <!-- Navigation -->
    <nav class="sidebar-nav py-3">
        <!-- Main Section -->
        <div class="section mb-4" id="sidebar-main">
            <div class="section-header px-4 py-2 text-uppercase small text-muted fw-semibold d-flex justify-content-between align-items-center">
                <span>Main</span>
                <i class="fas fa-chevron-down collapse-icon"></i>
            </div>
            <div class="section-items">
                <a href="/dashboard" class="nav-item px-4 py-2 d-flex align-items-center justify-content-between text-decoration-none
                    {{ request()->is('dashboard') ? 'active' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="nav-icon">📊</span>
                        <span class="nav-label ms-3">Dashboard</span>
                    </div>
                </a>

                @if(auth()->user()->hasAnyRole(['Encoder']))
                <a href="/encode" class="nav-item px-4 py-2 d-flex align-items-center justify-content-between text-decoration-none
                    {{ request()->is('encode') ? 'active' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="nav-icon">✍️</span>
                        <span class="nav-label ms-3">Encode Feedback</span>
                    </div>
                </a>
                @endif

                {{-- For Validation --}}
                @if(auth()->user()->hasAnyRole(['Validator']))
                <a href="/validation" class="nav-item px-4 py-2 d-flex align-items-center justify-content-between text-decoration-none {{ request()->is('validation') ? 'active' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="nav-icon">✓</span>
                        <span class="nav-label ms-3">For Validation</span>
                    </div>
                    @if($validationCount > 0)
                        <span class="badge bg-danger rounded-pill">{{ $validationCount }}</span>
                    @endif
                </a>
                @endif

                {{-- For Action --}}
                @if(auth()->user()->hasAnyRole(['Department Head']))
                <a href="/action" class="nav-item px-4 py-2 d-flex align-items-center justify-content-between text-decoration-none {{ request()->is('action') ? 'active' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="nav-icon">⚡</span>
                        <span class="nav-label ms-3">For Action</span>
                    </div>
                    @if($actionCount > 0)
                        <span class="badge bg-warning text-dark rounded-pill">{{ $actionCount }}</span>
                    @endif
                </a>
                @endif

                {{-- For Verification --}}
                @if(auth()->user()->hasAnyRole(['Verifier']))
                <a href="/verification" class="nav-item px-4 py-2 d-flex align-items-center justify-content-between text-decoration-none {{ request()->is('verification') ? 'active' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="nav-icon">🔍</span>
                        <span class="nav-label ms-3">For Verification</span>
                    </div>
                    @if($verificationCount > 0)
                        <span class="badge bg-info rounded-pill">{{ $verificationCount }}</span>
                    @endif
                </a>
                @endif
            </div>
        </div>

        <!-- Feedback for Action -->
        @if(auth()->user()->hasAnyRole(['Department Head']))
        <div class="section mb-4" id="sidebar-action">
            <div class="section-header px-4 py-2 text-uppercase small text-muted fw-semibold d-flex justify-content-between align-items-center">
                <span>Feedback for Action</span>
                <i class="fas fa-chevron-down collapse-icon"></i>
            </div>
            <div class="section-items">
                @foreach($sidebarDepartments as $department)
                    <a href="/department/{{ $department->dep_id }}" class="nav-item px-4 py-2 d-flex align-items-center justify-content-between text-decoration-none
                        {{ request()->is('department/'.$department->dep_id) ? 'active' : '' }}">
                        <div class="d-flex align-items-center">
                            <span class="nav-icon ms-2" style="width: 20px; text-align: center;">•</span>
                            <span class="nav-label ms-3">{{ $department->dep_name }}</span>
                        </div>

                        @if($department->pending_tickets_count > 0)
                            <span class="badge bg-danger rounded-pill">
                                {{ $department->pending_tickets_count }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Reports and Analytics -->
        @if(auth()->user()->hasAnyRole(['Reports Viewing']))
        <div class="section mb-4" id="sidebar-reports">
            <div class="section-header px-4 py-2 text-uppercase small text-muted fw-semibold d-flex justify-content-between align-items-center">
                <span>Reports and Analytics</span>
                <i class="fas fa-chevron-down collapse-icon"></i>
            </div>
            <div class="section-items">
                <a href="{{ route('reports.transactions') }}" class="nav-item px-4 py-2 d-flex align-items-center justify-content-between text-decoration-none
                    {{ request()->is('feedback-transactions') ? 'active' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="nav-icon">📋</span>
                        <span class="nav-label ms-3">Feedback Transactions</span>
                    </div>
                </a>
                <a href="{{ route('reports.analysis') }}" class="nav-item px-4 py-2 d-flex align-items-center justify-content-between text-decoration-none
                    {{ request()->is('transaction-analysis') ? 'active' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="nav-icon">📊</span>
                        <span class="nav-label ms-3">Transaction Analysis</span>
                    </div>
                </a>
                <a href="{{ route('reports.satisfaction') }}" class="nav-item px-4 py-2 d-flex align-items-center justify-content-between text-decoration-none
                    {{ request()->is('customer-satisfaction') ? 'active' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="nav-icon">😊</span>
                        <span class="nav-label ms-3">Customer Satisfaction</span>
                    </div>
                </a>
            </div>
        </div>
        @endif

        <!-- Administrator -->
        @if(auth()->user()->hasAnyRole(['Super Admin']))
        <div class="section" id="sidebar-admin">
            <div class="section-header px-4 py-2 text-uppercase small text-muted fw-semibold d-flex justify-content-between align-items-center">
                <span>Administrator</span>
                <i class="fas fa-chevron-down collapse-icon"></i>
            </div>
            <div class="section-items">
                <a href="{{ route('admin.users.index') }}" 
                class="nav-item px-4 py-2 d-flex align-items-center justify-content-between text-decoration-none
                    {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="nav-icon">👥</span>
                        <span class="nav-label ms-3">Manage Users</span>
                    </div>
                </a>

                <a href="{{ route('admin.departments.index') }}" 
                class="nav-item px-4 py-2 d-flex align-items-center justify-content-between text-decoration-none
                    {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="nav-icon">🏢</span>
                        <span class="nav-label ms-3">Manage Departments</span>
                    </div>
                </a>

                {{-- create a route for this later 
                <a href="/admin/feedback-parameters" 
                class="nav-item px-4 py-2 d-flex align-items-center justify-content-between text-decoration-none
                    {{ request()->is('admin/feedback-parameters*') ? 'active' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="nav-icon">⚙️</span>
                        <span class="nav-label ms-3">Feedback Parameters</span>
                    </div>
                </a> --}}

                {{-- AI Prediction Settings --}}
                <a href="/admin/ai-settings" 
                class="nav-item px-4 py-2 d-flex align-items-center justify-content-between text-decoration-none
                    {{ request()->is('admin/ai-settings*') ? 'active' : '' }}">
                    <div class="d-flex align-items-center">
                        <span class="nav-icon">🤖</span>
                        <span class="nav-label ms-3">AI Routing Settings</span>
                    </div>
                </a>
            </div>
        </div>
        @endif
    </nav>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar');

    const savedScrollPosition = localStorage.getItem('sidebarScrollPos');
    if (savedScrollPosition) {
        sidebar.scrollTop = savedScrollPosition;
    }

    window.addEventListener('beforeunload', function() {
        localStorage.setItem('sidebarScrollPos', sidebar.scrollTop);
    });

    // Collapsible sections
    document.querySelectorAll('.section').forEach(section => {
        const header = section.querySelector('.section-header');
        const items = section.querySelector('.section-items');
        const sectionId = section.id; // Grab the unique ID we just added
        
        if (!sectionId) return; // Safety check

        // Check localStorage to see if this specific section was saved as 'collapsed'
        const isCollapsed = localStorage.getItem('sidebar_collapse_' + sectionId) === 'true';

        // Apply saved state immediately on load
        if (isCollapsed) {
            section.classList.add('collapsed');
            items.style.maxHeight = '0';
        } else {
            items.style.maxHeight = items.scrollHeight + 'px';
        }

        // Listen for clicks to toggle and save
        header.addEventListener('click', function() {
            section.classList.toggle('collapsed');
            const currentlyCollapsed = section.classList.contains('collapsed');
            
            if (currentlyCollapsed) {
                items.style.maxHeight = '0';
            } else {
                items.style.maxHeight = items.scrollHeight + 'px';
            }

            // Save the new state to localStorage
            localStorage.setItem('sidebar_collapse_' + sectionId, currentlyCollapsed);
        });
    });
});
</script>