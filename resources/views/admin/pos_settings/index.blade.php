@extends('admin.layouts.master', ['is_active_parent' => 'user_management', 'is_active' => 'pos_settings'])
@section('title', 'POS Settings')
@section('content')
    <div class="d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-xxl">

                <div class="page-content-header mb-5">
                    <h2 class="table-title">POS Settings</h2>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.pos_settings.update') }}">
                    @csrf

                    <div class="card card-flush mb-5">
                        <div class="card-header pt-6">
                            <h3 class="card-title">Card fee</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-9">
                                <div class="col-md-4">
                                    <label class="required fs-6 fw-semibold mb-2">Fee per card sale</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" min="0" name="pos_card_fee"
                                               class="form-control"
                                               value="{{ old('pos_card_fee', number_format($cardFee, 2, '.', '')) }}">
                                    </div>
                                    <div class="text-muted fs-8 mt-2">Set to 0 to disable.</div>
                                </div>
                                <div class="col-md-8">
                                    <div class="alert alert-light-primary d-flex align-items-start h-100 mb-0">
                                        <i class="fa-solid fa-circle-info me-3 fs-3 mt-1"></i>
                                        <div class="fs-7">
                                            <strong>Added on top of the services — the customer pays it.</strong>
                                            <div class="mt-2">
                                                Example with a $1.00 fee: a $30.00 card sale charges
                                                <strong>$31.00</strong> on the reader. The employee still earns
                                                on the full <strong>$30.00</strong> of services; the $1.00 is
                                                the shop's and never enters their commission. Any tip the
                                                customer adds on the reader is on top of that.
                                            </div>
                                            <div class="mt-2">
                                                <strong>Tip cents also go to fees.</strong> A card tip is credited
                                                to the employee in whole dollars only — a <strong>$12.70</strong>
                                                tip pays the employee <strong>$12.00</strong> and the
                                                <strong>$0.70</strong> joins the fees. On the example above the
                                                fees for that sale total <strong>$1.70</strong>.
                                            </div>
                                            <div class="mt-2 text-muted">
                                                <strong>Card only</strong> — cash and Zelle sales carry no fee and
                                                their tips are never rounded.
                                                The fee in force is saved on each order when it is created, so
                                                changing this value never rewrites past reports.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection
