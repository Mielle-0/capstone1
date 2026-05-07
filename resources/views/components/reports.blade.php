<div class="container-fluid p-0">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="text-secondary fw-bold mb-1">Reports & Analytics Command Center</h5>
            <span class="text-muted small">Executive overview of system performance and customer satisfaction</span>
        </div>
    </div>



    <h6 class="text-uppercase text-muted fw-bold mb-3">Month-to-Date Performance</h6>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-primary border-4 h-100">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted fw-bold mb-1">Total Intake</h6>
                    <h2 class="mb-0 text-dark">{{ number_format($totalFeedbackThisMonth ?? 0) }}</h2>
                    <small class="text-muted">New feedback this month</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-success border-4 h-100">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted fw-bold mb-1">Tickets Resolved</h6>
                    <h2 class="mb-0 text-dark">{{ number_format($resolvedTicketsThisMonth ?? 0) }}</h2>
                    <small class="text-muted">Successfully closed</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-warning border-4 h-100">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted fw-bold mb-1">Overdue Tickets</h6>
                    <h2 class="mb-0 text-danger">{{ number_format($overdueTicketsCount ?? 0) }}</h2>
                    <small class="text-danger fw-medium">Breached SLA</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-info border-4 h-100 bg-dark text-white">
                <div class="card-body">
                    <h6 class="text-uppercase text-white-50 fw-bold mb-1">Average CSAT</h6>
                    <div class="d-flex align-items-baseline gap-2">
                        <h2 class="mb-0 fw-bold">{{ number_format($averageCsat ?? 0, 1) }}</h2>
                        <span class="text-warning fs-5">★</span>
                    </div>
                    <small class="text-white-50">Out of 5.0</small>
                </div>
            </div>
        </div>
    </div>

    <hr class="mb-5 border-secondary opacity-25">


    <h6 class="text-uppercase text-muted fw-bold mb-3">Deep Dive Reports</h6>
    <div class="row mb-5">
        <div class="col-md-4">
            <a  class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100 hover-elevate transition-all bg-primary bg-opacity-10 text-primary">
                    <div class="card-body d-flex align-items-center gap-3 py-4">
                        <div class="fs-1">📋</div>
                        <div>
                            <h6 class="fw-bold mb-1">Feedback Transactions</h6>
                            <span class="small opacity-75">View the complete master list and audit trails</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-md-4">
            <a  class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100 hover-elevate transition-all bg-info bg-opacity-10 text-info">
                    <div class="card-body d-flex align-items-center gap-3 py-4">
                        <div class="fs-1">📊</div>
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">Transaction Analysis</h6>
                            <span class="small text-muted">Department workloads, resolution times, and SLA tracking</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a  class="text-decoration-none">
                <div class="card shadow-sm border-0 h-100 hover-elevate transition-all bg-success bg-opacity-10 text-success">
                    <div class="card-body d-flex align-items-center gap-3 py-4">
                        <div class="fs-1">⭐</div>
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">Customer Satisfaction</h6>
                            <span class="small text-muted">CSAT scores, net promoter, and feedback ratings</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    
    <style>
        .hover-elevate:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
            cursor: pointer;
        }
        .transition-all {
            transition: all 0.3s ease-in-out;
        }
    </style>
</div>