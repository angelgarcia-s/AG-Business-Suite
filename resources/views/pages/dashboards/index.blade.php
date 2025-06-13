@extends('layouts.master')

@section('styles')

          
@endsection

@section('content')

               <!-- Start::page-header -->
               <div class="md:flex block items-center justify-between my-[1.5rem] page-header-breadcrumb">
                    <div>
                      <p class="font-semibold text-[1.125rem] text-defaulttextcolor dark:text-defaulttextcolor/70 !mb-0 ">Welcome back, Json Taylor !</p>
                      <p class="font-normal text-[#8c9097] dark:text-white/50 text-[0.813rem]">Track your sales activity, leads and deals here.</p>
                    </div>
                    <div class="btn-list md:mt-0 mt-2">
                      <button type="button"
                        class="ti-btn bg-primary text-white btn-wave !font-medium !me-[0.375rem] !ms-0 !text-[0.85rem] !rounded-[0.35rem] !py-[0.51rem] !px-[0.86rem] shadow-none">
                        <i class="ri-filter-3-fill  inline-block"></i>Filters
                      </button>
                      <button type="button"
                        class="ti-btn ti-btn-outline-secondary btn-wave !font-medium  !me-[0.375rem]  !ms-0 !text-[0.85rem] !rounded-[0.35rem] !py-[0.51rem] !px-[0.86rem] shadow-none">
                        <i class="ri-upload-cloud-line  inline-block"></i>Export
                      </button>
                    </div>
                  </div>
                  <!-- End::page-header -->

                  <div class="row row-sm mt-lg-4">
                      <div class="col-sm-12 col-lg-12 col-xl-12">
                            <div class="card bg-primary rounded-sm">
                                  <div class="card-body p-4">
                                        <span class="text-white">NOTE:</span>      
                                        <p class="text-white mt-2 mb-0">Thank you for choosing our template. if you want to create your own customised project take a refrence of our project and you can implement here.</p>              
                                  </div>
                            </div>
                      </div>
                  </div>

@endsection

@section('scripts')


@endsection