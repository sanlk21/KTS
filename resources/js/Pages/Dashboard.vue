<template>
    <AuthenticatedLayout>
        <Head title="Dashboard" />

        <div class="min-h-screen bg-gradient-to-br from-gray-50 via-gray-100 to-gray-200 relative overflow-hidden">
            <!-- Animated Background Effects -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute w-96 h-96 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob top-0 left-0"></div>
                <div class="absolute w-96 h-96 bg-gray-400 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob animation-delay-2000 top-0 right-0"></div>
                <div class="absolute w-96 h-96 bg-slate-400 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob animation-delay-4000 bottom-0 left-1/2"></div>
            </div>

            <div class="container mx-auto p-6 relative z-10">
                <!-- Futuristic Header -->
                <div class="mb-8 bg-white rounded-3xl p-6 border border-gray-200 shadow-2xl transform hover:scale-[1.02] transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <!-- <div class="relative group">
                                <div class="absolute -inset-1 bg-gradient-to-r from-gray-600 to-slate-600 rounded-2xl blur opacity-50 group-hover:opacity-75 transition duration-300 animate-pulse"></div>
                                <div class="relative w-20 h-20 bg-gradient-to-br from-gray-700 via-slate-700 to-gray-800 rounded-2xl flex items-center justify-center shadow-lg">
                                    <svg class="w-12 h-12 text-white transform group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </div>
                            </div> -->
                            <div>
                                <h1 class="text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-gray-700 via-slate-700 to-gray-900 animate-gradient">
                                    KTS Transport Hub
                                </h1>
                                <p class="text-sm text-gray-600 font-semibold tracking-wider">Advanced Fleet Management System</p>
                                <div class="flex items-center mt-2 space-x-2">
                                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                    <span class="text-xs text-green-600">System Online</span>
                                </div>
                            </div>
                        </div>
                        <Link :href="route('trips.create')"
                            class="group relative overflow-hidden bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 hover:from-blue-700 hover:via-blue-800 hover:to-blue-900 text-white font-bold py-4 px-8 rounded-2xl shadow-2xl transition-all duration-300 transform hover:scale-105 hover:shadow-blue-500/50">
                            <div class="absolute inset-0 bg-white/20 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left duration-300"></div>
                            <div class="relative flex items-center space-x-3">
                                <svg class="w-6 h-6 transform group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span>New Trip</span>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- Loading State with Futuristic Spinner -->
                <div v-if="loading" class="flex justify-center items-center py-20">
                    <div class="relative">
                        <div class="w-24 h-24 border-4 border-gray-300 border-t-gray-700 rounded-full animate-spin"></div>
                        <div class="absolute inset-0 w-24 h-24 border-4 border-gray-200 border-t-gray-600 rounded-full animate-spin-slow"></div>
                        <div class="absolute inset-3 w-18 h-18 border-4 border-gray-100 border-t-gray-500 rounded-full animate-spin-reverse"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="w-8 h-8 bg-gradient-to-r from-gray-500 to-gray-700 rounded-full animate-pulse"></div>
                        </div>
                    </div>
                </div>

                <div v-else class="space-y-8">
                    <!-- Enhanced Summary Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <Link :href="route('tippers.index')"
                            class="group relative overflow-hidden bg-white border border-gray-200 rounded-3xl p-6 cursor-pointer hover:shadow-2xl hover:shadow-blue-500/30 transition-all duration-500 transform hover:-translate-y-2">
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-50/0 to-blue-100/0 group-hover:from-blue-50/50 group-hover:to-blue-100/50 transition-all duration-500"></div>
                            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-100/30 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-500"></div>
                            <div class="relative flex items-center justify-between">
                                <div class="space-y-2">
                                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-4xl font-black text-gray-800 group-hover:scale-110 inline-block transition-transform duration-300">
                                            {{ summary.tippers }}
                                        </h3>
                                        <p class="text-sm text-gray-600 font-semibold tracking-wide">Total Tippers</p>
                                    </div>
                                </div>
                                <div class="text-gray-300 group-hover:text-blue-500 transition-colors duration-300">
                                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                        </Link>

                        <Link :href="route('drivers.index')"
                            class="group relative overflow-hidden bg-white border border-gray-200 rounded-3xl p-6 cursor-pointer hover:shadow-2xl hover:shadow-green-500/30 transition-all duration-500 transform hover:-translate-y-2">
                            <div class="absolute inset-0 bg-gradient-to-br from-green-50/0 to-green-100/0 group-hover:from-green-50/50 group-hover:to-green-100/50 transition-all duration-500"></div>
                            <div class="absolute top-0 right-0 w-32 h-32 bg-green-100/30 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-500"></div>
                            <div class="relative flex items-center justify-between">
                                <div class="space-y-2">
                                    <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-700 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-4xl font-black text-gray-800 group-hover:scale-110 inline-block transition-transform duration-300">
                                            {{ summary.drivers }}
                                        </h3>
                                        <p class="text-sm text-gray-600 font-semibold tracking-wide">Total Drivers</p>
                                    </div>
                                </div>
                                <div class="text-gray-300 group-hover:text-green-500 transition-colors duration-300">
                                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                        </Link>

                        <Link :href="route('plants.index')"
                            class="group relative overflow-hidden bg-white border border-gray-200 rounded-3xl p-6 cursor-pointer hover:shadow-2xl hover:shadow-orange-500/30 transition-all duration-500 transform hover:-translate-y-2">
                            <div class="absolute inset-0 bg-gradient-to-br from-orange-50/0 to-orange-100/0 group-hover:from-orange-50/50 group-hover:to-orange-100/50 transition-all duration-500"></div>
                            <div class="absolute top-0 right-0 w-32 h-32 bg-orange-100/30 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-500"></div>
                            <div class="relative flex items-center justify-between">
                                <div class="space-y-2">
                                    <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-orange-700 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-4xl font-black text-gray-800 group-hover:scale-110 inline-block transition-transform duration-300">
                                            {{ summary.plants }}
                                        </h3>
                                        <p class="text-sm text-gray-600 font-semibold tracking-wide">Total Plants</p>
                                    </div>
                                </div>
                                <div class="text-gray-300 group-hover:text-orange-500 transition-colors duration-300">
                                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                        </Link>

                        <Link :href="route('trips.index')"
                            class="group relative overflow-hidden bg-white border border-gray-200 rounded-3xl p-6 cursor-pointer hover:shadow-2xl hover:shadow-indigo-500/30 transition-all duration-500 transform hover:-translate-y-2">
                            <div class="absolute inset-0 bg-gradient-to-br from-indigo-50/0 to-indigo-100/0 group-hover:from-indigo-50/50 group-hover:to-indigo-100/50 transition-all duration-500"></div>
                            <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-100/30 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-500"></div>
                            <div class="relative flex items-center justify-between">
                                <div class="space-y-2">
                                    <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-4xl font-black text-gray-800 group-hover:scale-110 inline-block transition-transform duration-300">
                                            {{ summary.trips }}
                                        </h3>
                                        <p class="text-sm text-gray-600 font-semibold tracking-wide">Total Trips</p>
                                    </div>
                                </div>
                                <div class="text-gray-300 group-hover:text-indigo-500 transition-colors duration-300">
                                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                        </Link>
                    </div>

                    <!-- License Expiry Alerts -->
                    <div v-if="expiringTippers.length" class="bg-red-50 border-l-4 border-red-500 p-6 rounded-2xl shadow-2xl animate-pulse-slow">
                        <div class="flex items-center mb-4">
                            <div class="relative mr-3">
                                <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center animate-ping absolute"></div>
                                <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center relative">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.084 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                    </svg>
                                </div>
                            </div>
                            <h2 class="text-xl font-bold text-red-700">
                                License Expiry Alerts ({{ expiringTippers.length }})
                            </h2>
                        </div>
                        <div class="space-y-3">
                            <div v-for="(tipper, index) in expiringTippers" :key="tipper.tipper_number"
                                class="bg-white border border-red-200 p-4 rounded-xl hover:bg-red-50 transition-all duration-300 transform hover:scale-[1.02]"
                                :style="{ animationDelay: `${index * 100}ms` }">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-700 rounded-full flex items-center justify-center shadow-lg">
                                            <span class="text-white font-bold text-lg">{{ tipper.tipper_number.charAt(0) }}</span>
                                        </div>
                                        <div>
                                            <p class="font-bold text-red-700 text-lg">Tipper {{ tipper.tipper_number }}</p>
                                            <p class="text-sm text-red-600">Expires: {{ formatDate(tipper.license_expiry) }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <div class="bg-red-100 border border-red-300 px-4 py-2 rounded-full">
                                            <span class="text-red-700 font-bold">{{ getDaysUntilExpiry(tipper.license_expiry) }}</span>
                                            <span class="text-red-600 text-xs ml-1">days</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Period Selector -->
                    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-2xl">
                        <div class="flex items-center justify-between flex-wrap gap-4">
                            <div class="flex items-center space-x-4">
                                <label class="text-gray-700 font-bold text-lg">Analysis Period:</label>
                                <div class="relative">
                                    <select v-model="period" @change="fetchReports"
                                        class="appearance-none bg-gray-50 border border-gray-300 text-gray-800 rounded-xl px-6 py-3 pr-10 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent font-semibold cursor-pointer hover:bg-gray-100 transition-all duration-300">
                                        <option value="daily">Daily</option>
                                        <option value="weekly">Weekly</option>
                                        <option value="monthly">Monthly</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <Link :href="route('trips.reports')"
                                class="group relative overflow-hidden bg-gradient-to-r from-gray-600 to-gray-800 hover:from-gray-700 hover:to-gray-900 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105">
                                <span class="relative z-10 flex items-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span>Detailed Reports</span>
                                </span>
                            </Link>
                        </div>
                    </div>

                    <!-- Charts Section -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Plant Deliveries Chart -->
                        <div class="bg-white border border-gray-200 rounded-3xl p-6 shadow-2xl hover:shadow-blue-500/20 transition-all duration-500 transform hover:-translate-y-1">
                            <div class="flex items-center mb-6">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl flex items-center justify-center mr-3">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                                <h2 class="text-xl font-bold text-gray-800">
                                    Deliveries by Plant
                                </h2>
                            </div>
                            <div class="relative" style="height: 300px;">
                                <canvas ref="plantChart" class="w-full h-full"></canvas>
                            </div>
                        </div>

                        <!-- Driver Salaries Chart -->
                        <div class="bg-white border border-gray-200 rounded-3xl p-6 shadow-2xl hover:shadow-green-500/20 transition-all duration-500 transform hover:-translate-y-1">
                            <div class="flex items-center mb-6">
                                <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-700 rounded-xl flex items-center justify-center mr-3">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <h2 class="text-xl font-bold text-gray-800">
                                    Driver Salaries
                                </h2>
                            </div>
                            <div class="relative" style="height: 300px;">
                                <canvas ref="salaryChart" class="w-full h-full"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Summary -->
                    <div class="bg-white border border-gray-200 rounded-3xl p-6 shadow-2xl">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-gradient-to-br from-gray-600 to-gray-800 rounded-xl flex items-center justify-center mr-3 animate-pulse-slow">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-800">
                                Financial Overview
                            </h2>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div class="group relative overflow-hidden bg-gradient-to-br from-green-500 to-green-700 border border-green-600 rounded-2xl p-6 transform hover:scale-105 transition-all duration-300 hover:shadow-2xl hover:shadow-green-500/40">
                                <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
                                <div class="relative">
                                    <div class="flex items-center justify-between mb-3">
                                        <p class="text-green-100 text-sm font-semibold uppercase tracking-wider">Total Revenue</p>
                                        <div class="bg-white/20 rounded-full p-2">
                                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <p class="text-3xl font-black text-white mb-1">Rs {{ formatCurrency(reports.income.total_revenue) }}</p>
                                    <div class="flex items-center text-green-100 text-sm">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                        </svg>
                                        <span class="font-semibold">Active</span>
                                    </div>
                                </div>
                            </div>

                            <div class="group relative overflow-hidden bg-gradient-to-br from-blue-500 to-blue-700 border border-blue-600 rounded-2xl p-6 transform hover:scale-105 transition-all duration-300 hover:shadow-2xl hover:shadow-blue-500/40">
                                <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
                                <div class="relative">
                                    <div class="flex items-center justify-between mb-3">
                                        <p class="text-blue-100 text-sm font-semibold uppercase tracking-wider">Total Expenses</p>
                                        <div class="bg-white/20 rounded-full p-2">
                                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <p class="text-3xl font-black text-white mb-1">Rs {{ formatCurrency(reports.income.total_expenses) }}</p>
                                    <div class="flex items-center text-blue-100 text-sm">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span class="font-semibold">Tracked</span>
                                    </div>
                                </div>
                            </div>

                            <div class="group relative overflow-hidden bg-gradient-to-br from-indigo-500 to-indigo-700 border border-indigo-600 rounded-2xl p-6 transform hover:scale-105 transition-all duration-300 hover:shadow-2xl hover:shadow-indigo-500/40">
                                <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
                                <div class="relative">
                                    <div class="flex items-center justify-between mb-3">
                                        <p class="text-indigo-100 text-sm font-semibold uppercase tracking-wider">Net Profit</p>
                                        <div class="bg-white/20 rounded-full p-2">
                                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <p class="text-3xl font-black text-white mb-1">Rs {{ formatCurrency(reports.income.net_profit) }}</p>
                                    <div class="flex items-center text-indigo-100 text-sm">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                        </svg>
                                        <span class="font-semibold">Optimized</span>
                                    </div>
                                </div>
                            </div>

                            <div class="group relative overflow-hidden bg-gradient-to-br from-orange-500 to-orange-700 border border-orange-600 rounded-2xl p-6 transform hover:scale-105 transition-all duration-300 hover:shadow-2xl hover:shadow-orange-500/40">
                                <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
                                <div class="relative">
                                    <div class="flex items-center justify-between mb-3">
                                        <p class="text-orange-100 text-sm font-semibold uppercase tracking-wider">Pending Payments</p>
                                        <div class="bg-white/20 rounded-full p-2">
                                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <p class="text-3xl font-black text-white mb-1">Rs {{ formatCurrency(reports.income.pending) }}</p>
                                    <div class="flex items-center text-orange-100 text-sm">
                                        <svg class="w-4 h-4 mr-1 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="font-semibold">Awaiting</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions Panel -->
                    <div class="bg-white border border-gray-200 rounded-3xl p-6 shadow-2xl">
                        <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                            <svg class="w-6 h-6 text-gray-700 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Quick Actions
                        </h2>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <Link :href="route('trips.create')"
                                class="group bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-4 hover:from-blue-100 hover:to-blue-200 transition-all duration-300 transform hover:scale-105 cursor-pointer hover:shadow-lg">
                                <div class="flex flex-col items-center text-center space-y-2">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </div>
                                    <span class="text-blue-700 font-semibold text-sm">New Trip</span>
                                </div>
                            </Link>

                            <Link :href="route('tippers.create')"
                                class="group bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-xl p-4 hover:from-green-100 hover:to-green-200 transition-all duration-300 transform hover:scale-105 cursor-pointer hover:shadow-lg">
                                <div class="flex flex-col items-center text-center space-y-2">
                                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-700 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                    </div>
                                    <span class="text-green-700 font-semibold text-sm">Add Tipper</span>
                                </div>
                            </Link>

                            <Link :href="route('drivers.create')"
                                class="group bg-gradient-to-br from-indigo-50 to-indigo-100 border border-indigo-200 rounded-xl p-4 hover:from-indigo-100 hover:to-indigo-200 transition-all duration-300 transform hover:scale-105 cursor-pointer hover:shadow-lg">
                                <div class="flex flex-col items-center text-center space-y-2">
                                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                        </svg>
                                    </div>
                                    <span class="text-indigo-700 font-semibold text-sm">Add Driver</span>
                                </div>
                            </Link>

                            <Link :href="route('trips.reports')"
                                class="group bg-gradient-to-br from-orange-50 to-orange-100 border border-orange-200 rounded-xl p-4 hover:from-orange-100 hover:to-orange-200 transition-all duration-300 transform hover:scale-105 cursor-pointer hover:shadow-lg">
                                <div class="flex flex-col items-center text-center space-y-2">
                                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-700 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <span class="text-orange-700 font-semibold text-sm">View Reports</span>
                                </div>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { computed, ref, onMounted, nextTick } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    dashboardData: {
        type: Object,
        default: () => ({})
    }
})

// Reactive data
const loading = ref(false)
const period = ref('monthly')
const plantChart = ref(null)
const salaryChart = ref(null)
const error = ref(null)

let chartInstances = {}

// Initialize data from props
const dashboardData = ref(props.dashboardData || {})

// Computed properties
const summary = computed(() => ({
    tippers: dashboardData.value.summary?.tippers || 0,
    drivers: dashboardData.value.summary?.drivers || 0,
    plants: dashboardData.value.summary?.plants || 0,
    trips: dashboardData.value.summary?.trips || 0
}))

const expiringTippers = computed(() => dashboardData.value.expiring_tippers || [])

const reports = computed(() => ({
    deliveries_by_plant: dashboardData.value.reports?.deliveries_by_plant || [],
    driver_salaries: dashboardData.value.reports?.driver_salaries || [],
    income: {
        total_revenue: dashboardData.value.reports?.income?.total_revenue || 0,
        total_expenses: dashboardData.value.reports?.income?.total_expenses || 0,
        net_profit: dashboardData.value.reports?.income?.net_profit || 0,
        pending: dashboardData.value.reports?.income?.pending || 0
    }
}))

// Methods
const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-IN').format(value || 0)
}

const formatDate = (dateString) => {
    if (!dateString) return 'N/A'
    return new Date(dateString).toLocaleDateString('en-IN', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const getDaysUntilExpiry = (dateString) => {
    if (!dateString) return 0
    const today = new Date()
    const expiryDate = new Date(dateString)
    const diffTime = expiryDate - today
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
    return Math.max(0, diffDays)
}

const fetchReports = () => {
    loading.value = true

    router.get(route('dashboard'), { period: period.value }, {
        preserveState: true,
        onSuccess: (page) => {
            dashboardData.value = page.props.dashboardData || {}
            loading.value = false
            nextTick(() => {
                destroyCharts()
                renderCharts()
            })
        },
        onError: () => {
            loading.value = false
            error.value = 'Failed to load dashboard data'
        }
    })
}

const destroyCharts = () => {
    Object.values(chartInstances).forEach(chart => {
        if (chart) {
            chart.destroy()
        }
    })
    chartInstances = {}
}

const renderCharts = () => {
    if (!window.Chart) return

    // Plant Deliveries Chart
    if (plantChart.value && reports.value.deliveries_by_plant.length > 0) {
        const ctx = plantChart.value.getContext('2d')
        chartInstances.plant = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: reports.value.deliveries_by_plant.map(p => p.name),
                datasets: [{
                    label: 'Trips',
                    data: reports.value.deliveries_by_plant.map(p => p.trip_count),
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgba(37, 99, 235, 1)',
                    borderWidth: 2,
                    borderRadius: 8,
                    hoverBackgroundColor: 'rgba(37, 99, 235, 1)',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.95)',
                        titleColor: '#1f2937',
                        bodyColor: '#374151',
                        borderColor: '#e5e7eb',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return `Trips: ${context.raw}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#6b7280',
                            font: {
                                size: 11
                            }
                        },
                        title: {
                            display: true,
                            text: 'Number of Trips',
                            color: '#374151',
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6b7280',
                            font: {
                                size: 11
                            }
                        },
                        title: {
                            display: true,
                            text: 'Plants',
                            color: '#374151',
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    }
                }
            }
        })
    }

    // Driver Salaries Chart
    if (salaryChart.value && reports.value.driver_salaries.length > 0) {
        const ctx = salaryChart.value.getContext('2d')
        chartInstances.salary = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: reports.value.driver_salaries.map(d => d.name),
                datasets: [{
                    label: 'Salary (Rs)',
                    data: reports.value.driver_salaries.map(d => d.salary),
                    backgroundColor: 'rgba(34, 197, 94, 0.8)',
                    borderColor: 'rgba(22, 163, 74, 1)',
                    borderWidth: 2,
                    borderRadius: 8,
                    hoverBackgroundColor: 'rgba(22, 163, 74, 1)',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.95)',
                        titleColor: '#1f2937',
                        bodyColor: '#374151',
                        borderColor: '#e5e7eb',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return `Salary: Rs ${new Intl.NumberFormat('en-IN').format(context.raw)}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#6b7280',
                            font: {
                                size: 11
                            },
                            callback: function(value) {
                                return 'Rs ' + new Intl.NumberFormat('en-IN').format(value);
                            }
                        },
                        title: {
                            display: true,
                            text: 'Salary Amount (Rs)',
                            color: '#374151',
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6b7280',
                            font: {
                                size: 11
                            }
                        },
                        title: {
                            display: true,
                            text: 'Drivers',
                            color: '#374151',
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    }
                }
            }
        })
    }
}

// Lifecycle
onMounted(() => {
    // Load Chart.js
    if (!window.Chart) {
        const script = document.createElement('script')
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js'
        script.onload = () => {
            nextTick(() => {
                renderCharts()
            })
        }
        document.head.appendChild(script)
    } else {
        nextTick(() => {
            renderCharts()
        })
    }
})
</script>

<style scoped>
@keyframes blob {
    0%, 100% {
        transform: translate(0, 0) scale(1);
    }
    25% {
        transform: translate(20px, -20px) scale(1.1);
    }
    50% {
        transform: translate(-20px, 20px) scale(0.9);
    }
    75% {
        transform: translate(20px, 20px) scale(1.05);
    }
}

@keyframes gradient {
    0%, 100% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

.animate-gradient {
    background-size: 200% 200%;
    animation: gradient 3s ease infinite;
}

.animate-spin-slow {
    animation: spin 3s linear infinite;
}

.animate-spin-reverse {
    animation: spin 2s linear infinite reverse;
}

.animate-pulse-slow {
    animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
</style>