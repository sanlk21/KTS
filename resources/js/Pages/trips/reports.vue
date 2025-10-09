<template>
  <AuthenticatedLayout>
    <Head title="Trip Reports" />

    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50">
      <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8 animate-fade-in">
          <div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Trip Reports & Analytics</h1>
            <p class="text-gray-500">Comprehensive insights into your business performance</p>
          </div>
          <div class="flex space-x-3">
            <Link 
              :href="route('trips.index')" 
              class="group relative px-6 py-3 bg-white text-gray-700 font-semibold rounded-xl shadow-sm hover:shadow-md border border-gray-200 transition-all duration-300 hover:-translate-y-0.5"
            >
              <span class="flex items-center">
                <svg class="w-5 h-5 mr-2 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Trips
              </span>
            </Link>
            <button 
              @click="exportReport" 
              class="group relative px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-semibold rounded-xl shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5"
            >
              <span class="flex items-center">
                <svg class="w-5 h-5 mr-2 transition-transform group-hover:translate-y-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export Report
              </span>
            </button>
          </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8 animate-slide-up" style="animation-delay: 0.1s">
          <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="transform transition-all duration-200 hover:scale-105">
              <label class="block text-sm font-semibold text-gray-700 mb-2">Search</label>
              <div class="relative">
                <input
                  v-model="filters.search"
                  type="text"
                  placeholder="Search driver, plant, tipper..."
                  class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                />
                <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
              </div>
            </div>
            <div class="transform transition-all duration-200 hover:scale-105">
              <label class="block text-sm font-semibold text-gray-700 mb-2">Period</label>
              <select
                v-model="filters.period"
                @change="fetchReports"
                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 cursor-pointer"
              >
                <option value="today">Today</option>
                <option value="yesterday">Yesterday</option>
                <option value="weekly">This Week</option>
                <option value="monthly">This Month</option>
                <option value="quarterly">This Quarter</option>
                <option value="yearly">This Year</option>
                <option value="custom">Custom Range</option>
              </select>
            </div>
            <div v-if="filters.period === 'custom'" class="transform transition-all duration-200 hover:scale-105">
              <label class="block text-sm font-semibold text-gray-700 mb-2">Start Date</label>
              <input
                v-model="filters.startDate"
                type="date"
                @change="fetchReports"
                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
              />
            </div>
            <div v-if="filters.period === 'custom'" class="transform transition-all duration-200 hover:scale-105">
              <label class="block text-sm font-semibold text-gray-700 mb-2">End Date</label>
              <input
                v-model="filters.endDate"
                type="date"
                @change="fetchReports"
                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
              />
            </div>
            <div class="transform transition-all duration-200 hover:scale-105">
              <label class="block text-sm font-semibold text-gray-700 mb-2">Plant Filter</label>
              <select
                v-model="filters.plantId"
                @change="fetchReports"
                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 cursor-pointer"
              >
                <option value="">All Plants</option>
                <option v-for="plant in plants" :key="plant.id" :value="plant.id">
                  {{ plant.name }}
                </option>
              </select>
            </div>
          </div>
          <!-- Loading Indicator -->
          <div v-if="loading" class="mt-6 text-center">
            <div class="inline-flex items-center px-6 py-3 bg-blue-50 rounded-xl">
              <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span class="text-blue-700 font-medium">Loading data...</span>
            </div>
          </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div class="bg-white rounded-2xl shadow-lg border border-blue-100 p-6 transform transition-all duration-300 hover:scale-105 hover:shadow-xl animate-slide-up" style="animation-delay: 0.2s">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-blue-600 text-sm font-semibold mb-1">Total Trips</p>
                <p class="text-4xl font-bold text-gray-900">{{ summary.totalTrips || 0 }}</p>
              </div>
              <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-4 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                  <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v8a1 1 0 001 1h2a1 1 0 001-1V8a1 1 0 00-1-1h-2z"/>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-2xl shadow-lg border border-emerald-100 p-6 transform transition-all duration-300 hover:scale-105 hover:shadow-xl animate-slide-up" style="animation-delay: 0.3s">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-emerald-600 text-sm font-semibold mb-1">Total Revenue</p>
                <p class="text-4xl font-bold text-gray-900">Rs. {{ formatCurrency(summary.totalRevenue) }}</p>
              </div>
              <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-4 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-2xl shadow-lg border border-orange-100 p-6 transform transition-all duration-300 hover:scale-105 hover:shadow-xl animate-slide-up" style="animation-delay: 0.4s">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-orange-600 text-sm font-semibold mb-1">Total Expenses</p>
                <p class="text-4xl font-bold text-gray-900">Rs. {{ formatCurrency(summary.totalExpenses) }}</p>
              </div>
              <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-4 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-2xl shadow-lg border border-purple-100 p-6 transform transition-all duration-300 hover:scale-105 hover:shadow-xl animate-slide-up" style="animation-delay: 0.5s">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-purple-600 text-sm font-semibold mb-1">Net Income</p>
                <p class="text-4xl font-bold" :class="summary.netIncome >= 0 ? 'text-gray-900' : 'text-red-600'">
                  Rs. {{ formatCurrency(summary.netIncome) }}
                </p>
              </div>
              <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-4 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
          <!-- Revenue Trend Chart -->
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transform transition-all duration-300 hover:shadow-lg animate-slide-up" style="animation-delay: 0.6s">
            <div class="flex items-center mb-4">
              <div class="w-1 h-8 bg-gradient-to-b from-blue-500 to-purple-500 rounded-full mr-3"></div>
              <h3 class="text-xl font-bold text-gray-900">Revenue Trend</h3>
            </div>
            <div class="w-full" style="height: 400px; min-height: 400px;">
              <canvas ref="revenueChart" class="w-full h-full"></canvas>
            </div>
          </div>

          <!-- Plant Distribution Chart -->
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transform transition-all duration-300 hover:shadow-lg animate-slide-up" style="animation-delay: 0.7s">
            <div class="flex items-center mb-4">
              <div class="w-1 h-8 bg-gradient-to-b from-emerald-500 to-teal-500 rounded-full mr-3"></div>
              <h3 class="text-xl font-bold text-gray-900">Trips by Plant</h3>
            </div>
            <div class="w-full" style="height: 400px; min-height: 400px;">
              <canvas ref="plantChart" class="w-full h-full"></canvas>
            </div>
          </div>
        </div>

        <!-- Driver Performance Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-8 overflow-hidden animate-slide-up" style="animation-delay: 0.8s">
          <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
            <div class="flex items-center">
              <div class="w-1 h-8 bg-gradient-to-b from-blue-500 to-indigo-500 rounded-full mr-3"></div>
              <h3 class="text-xl font-bold text-gray-900">
                Driver Performance
                <span v-if="selectedPlantName" class="text-sm font-normal text-gray-600 ml-2">({{ selectedPlantName }})</span>
                <span v-else class="text-sm font-normal text-gray-600 ml-2">(All Plants)</span>
              </h3>
            </div>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Driver</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Total Trips</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Total Salary</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Avg per Trip</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Performance</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-50">
                <tr v-if="filteredDriverStats.length === 0">
                  <td colspan="5" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center">
                      <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                      </svg>
                      <p class="text-gray-500 font-medium">{{ loading ? 'Loading drivers...' : 'No drivers found for the selected criteria' }}</p>
                    </div>
                  </td>
                </tr>
                <tr v-for="driver in filteredDriverStats" :key="driver.id" class="hover:bg-blue-50 transition-colors duration-200">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center text-white font-bold text-lg shadow-md">
                        {{ driver.name.charAt(0) }}
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-semibold text-gray-900">{{ driver.name }}</div>
                        <div class="text-xs text-gray-500">{{ driver.phone && driver.phone !== 'N/A' ? driver.phone : 'No contact' }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold bg-blue-100 text-blue-800">
                      {{ driver.total_trips }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                    Rs. {{ formatCurrency(driver.total_salary) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                    Rs. {{ formatCurrency(driver.avg_per_trip) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="w-24 bg-gray-200 rounded-full h-2.5 mr-3 overflow-hidden">
                        <div
                          class="bg-gradient-to-r from-emerald-500 to-teal-600 h-2.5 rounded-full transition-all duration-500"
                          :style="{ width: Math.min(100, (driver.total_trips / maxDriverTrips) * 100) + '%' }"
                        ></div>
                      </div>
                      <span class="text-sm font-semibold text-gray-700">{{ Math.round((driver.total_trips / maxDriverTrips) * 100) }}%</span>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Tipper Performance Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-8 overflow-hidden animate-slide-up" style="animation-delay: 0.9s">
          <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-yellow-50 to-orange-50">
            <div class="flex items-center">
              <div class="w-1 h-8 bg-gradient-to-b from-yellow-500 to-orange-500 rounded-full mr-3"></div>
              <h3 class="text-xl font-bold text-gray-900">
                Tipper Performance
                <span v-if="selectedPlantName" class="text-sm font-normal text-gray-600 ml-2">({{ selectedPlantName }})</span>
                <span v-else class="text-sm font-normal text-gray-600 ml-2">(All Plants)</span>
              </h3>
            </div>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tipper</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Total Trips</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Total Revenue</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Utilization</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Efficiency</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Profit Margin</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-50">
                <tr v-if="filteredTipperStats.length === 0">
                  <td colspan="6" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center">
                      <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                      </svg>
                      <p class="text-gray-500 font-medium">{{ loading ? 'Loading tippers...' : 'No tippers found for the selected criteria' }}</p>
                    </div>
                  </td>
                </tr>
                <tr v-for="tipper in filteredTipperStats" :key="tipper.id" class="hover:bg-yellow-50 transition-colors duration-200">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-2xl flex items-center justify-center text-white font-bold text-lg shadow-md">
                        {{ tipper.tipper_number.charAt(0) }}
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-semibold text-gray-900">{{ tipper.tipper_number }}</div>
                        <div class="text-xs text-gray-500">
                          Capacity: {{ tipper.capacity !== 'N/A' ? tipper.capacity : 'Unknown' }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold bg-yellow-100 text-yellow-800">
                      {{ tipper.total_trips }}
                    </span>
                    <div class="text-xs text-gray-500 mt-1">{{ tipper.total_batches }} batches</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <div class="font-semibold text-gray-900">Rs. {{ formatCurrency(tipper.total_revenue) }}</div>
                    <div class="text-xs text-emerald-600 font-medium">Profit: Rs. {{ formatCurrency(tipper.total_profit) }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <div class="font-semibold text-gray-900">{{ tipper.utilization_rate }} trips/day</div>
                    <div class="text-xs text-gray-500">{{ tipper.active_days }} active days</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <div class="font-semibold text-gray-900">Rs. {{ formatCurrency(tipper.efficiency_score) }}/trip</div>
                    <div class="text-xs text-gray-500">{{ tipper.unique_drivers }} drivers, {{ tipper.unique_plants }} plants</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="w-24 bg-gray-200 rounded-full h-2.5 mr-3 overflow-hidden">
                        <div
                          class="h-2.5 rounded-full transition-all duration-500"
                          :class="tipper.profit_margin >= 20 ? 'bg-gradient-to-r from-emerald-500 to-teal-600' : tipper.profit_margin >= 10 ? 'bg-gradient-to-r from-yellow-500 to-orange-500' : 'bg-gradient-to-r from-red-500 to-red-600'"
                          :style="{ width: Math.min(100, Math.abs(tipper.profit_margin)) + '%' }"
                        ></div>
                      </div>
                      <span class="text-sm font-semibold text-gray-700">{{ tipper.profit_margin }}%</span>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Plant Performance Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-8 overflow-hidden animate-slide-up" style="animation-delay: 1s">
          <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-teal-50">
            <div class="flex items-center">
              <div class="w-1 h-8 bg-gradient-to-b from-emerald-500 to-teal-500 rounded-full mr-3"></div>
              <h3 class="text-xl font-bold text-gray-900">
                Plant Performance
                <span v-if="selectedPlantName" class="text-sm font-normal text-gray-600 ml-2">({{ selectedPlantName }} only)</span>
                <span v-else class="text-sm font-normal text-gray-600 ml-2">(All Plants)</span>
              </h3>
            </div>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Plant</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Total Trips</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Total Amount</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Avg per Trip</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Market Share</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-50">
                <tr v-if="filteredPlantStats.length === 0">
                  <td colspan="5" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center">
                      <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                      </svg>
                      <p class="text-gray-500 font-medium">{{ loading ? 'Loading plants...' : 'No plants found for the selected criteria' }}</p>
                    </div>
                  </td>
                </tr>
                <tr v-for="plant in filteredPlantStats" :key="plant.id" class="hover:bg-emerald-50 transition-colors duration-200">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center text-white font-bold text-lg shadow-md">
                        {{ plant.name.charAt(0) }}
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-semibold text-gray-900">{{ plant.name }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-100 text-emerald-800">
                      {{ plant.total_trips }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                    Rs. {{ formatCurrency(plant.total_amount) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                    Rs. {{ formatCurrency(plant.avg_per_trip) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="w-24 bg-gray-200 rounded-full h-2.5 mr-3 overflow-hidden">
                        <div
                          class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2.5 rounded-full transition-all duration-500"
                          :style="{ width: (plant.total_amount / totalRevenue) * 100 + '%' }"
                        ></div>
                      </div>
                      <span class="text-sm font-semibold text-gray-700">{{ Math.round((plant.total_amount / totalRevenue) * 100) }}%</span>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Daily Income Breakdown -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up" style="animation-delay: 1.1s">
          <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-pink-50">
            <div class="flex items-center">
              <div class="w-1 h-8 bg-gradient-to-b from-purple-500 to-pink-500 rounded-full mr-3"></div>
              <h3 class="text-xl font-bold text-gray-900">
                Daily Income Breakdown
                <span v-if="selectedPlantName" class="text-sm font-normal text-gray-600 ml-2">({{ selectedPlantName }})</span>
                <span v-else class="text-sm font-normal text-gray-600 ml-2">(All Plants)</span>
              </h3>
            </div>
          </div>
          <div class="p-6">
            <div class="w-full" style="height: 500px; min-height: 500px;">
              <canvas ref="dailyIncomeChart" class="w-full h-full"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { computed, ref, onMounted, nextTick, watch } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  reportData: {
    type: Object,
    default: () => ({})
  },
  plants: {
    type: Array,
    default: () => []
  }
})

// Reactive data
const filters = ref({
  search: '',
  period: 'monthly',
  startDate: '',
  endDate: '',
  plantId: ''
})

const reportData = ref(props.reportData || {})
const loading = ref(false)
const revenueChart = ref(null)
const plantChart = ref(null)
const dailyIncomeChart = ref(null)
let chartInstances = {}

// Initialize filters from current report data
onMounted(() => {
  if (props.reportData?.selected_plant_id) {
    filters.value.plantId = props.reportData.selected_plant_id
  }
})

// Computed properties
const summary = computed(() => {
  const data = reportData.value
  return {
    totalTrips: data.summary?.total_trips || 0,
    totalRevenue: data.summary?.total_revenue || 0,
    totalExpenses: data.summary?.total_expenses || 0,
    netIncome: (data.summary?.total_revenue || 0) - (data.summary?.total_expenses || 0)
  }
})

const selectedPlantName = computed(() => {
  if (filters.value.plantId) {
    const plant = props.plants.find(p => p.id == filters.value.plantId)
    return plant ? plant.name : ''
  }
  return ''
})

const filteredDriverStats = computed(() => {
  const drivers = reportData.value.driver_stats || []
  if (!filters.value.search) return drivers

  return drivers.filter(driver =>
    driver.name.toLowerCase().includes(filters.value.search.toLowerCase()) ||
    (driver.phone && driver.phone !== 'N/A' && driver.phone.includes(filters.value.search))
  )
})

const filteredTipperStats = computed(() => {
  const tippers = reportData.value.tipper_stats || []
  if (!filters.value.search) return tippers

  return tippers.filter(tipper =>
    tipper.tipper_number.toLowerCase().includes(filters.value.search.toLowerCase()) ||
    (tipper.capacity && tipper.capacity !== 'N/A' && tipper.capacity.toLowerCase().includes(filters.value.search.toLowerCase()))
  )
})

const filteredPlantStats = computed(() => {
  const plants = reportData.value.plant_stats || []
  if (!filters.value.search) return plants

  return plants.filter(plant =>
    plant.name.toLowerCase().includes(filters.value.search.toLowerCase())
  )
})

const maxDriverTrips = computed(() => {
  const drivers = filteredDriverStats.value
  return Math.max(...drivers.map(d => d.total_trips), 1)
})

const totalRevenue = computed(() => {
  return filteredPlantStats.value.reduce((sum, plant) => sum + plant.total_amount, 0)
})

// Methods
const formatCurrency = (value) => {
  return new Intl.NumberFormat('en-IN').format(value || 0)
}

const fetchReports = () => {
  loading.value = true

  const params = {
    period: filters.value.period,
    plant_id: filters.value.plantId
  }

  if (filters.value.period === 'custom') {
    params.start_date = filters.value.startDate
    params.end_date = filters.value.endDate
  }

  router.get(route('trips.reports'), params, {
    preserveState: true,
    onSuccess: (page) => {
      reportData.value = page.props.reportData || {}
      loading.value = false
      nextTick(() => {
        destroyCharts()
        initCharts()
      })
    },
    onError: () => {
      loading.value = false
    }
  })
}

const exportReport = () => {
  const params = new URLSearchParams({
    period: filters.value.period,
    plant_id: filters.value.plantId || '',
    export: 'pdf'
  })

  if (filters.value.period === 'custom') {
    params.append('start_date', filters.value.startDate)
    params.append('end_date', filters.value.endDate)
  }

  window.open(`${route('trips.reports')}?${params.toString()}`, '_blank')
}

const destroyCharts = () => {
  Object.values(chartInstances).forEach(chart => {
    if (chart) {
      chart.destroy()
    }
  })
  chartInstances = {}
}

const initCharts = () => {
  if (!window.Chart) return

  // Revenue Trend Chart
  if (revenueChart.value) {
    const ctx = revenueChart.value.getContext('2d')
    chartInstances.revenue = new Chart(ctx, {
      type: 'line',
      data: {
        labels: reportData.value.revenue_trend?.labels || [],
        datasets: [
          {
            label: 'Revenue',
            data: reportData.value.revenue_trend?.revenue || [],
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true,
            borderWidth: 3,
            pointRadius: 5,
            pointHoverRadius: 7,
            pointBackgroundColor: '#3b82f6',
            pointBorderColor: '#fff',
            pointBorderWidth: 2
          },
          {
            label: 'Expenses',
            data: reportData.value.revenue_trend?.expenses || [],
            borderColor: '#ef4444',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            tension: 0.4,
            fill: true,
            borderWidth: 3,
            pointRadius: 5,
            pointHoverRadius: 7,
            pointBackgroundColor: '#ef4444',
            pointBorderColor: '#fff',
            pointBorderWidth: 2
          },
          {
            label: 'Profit',
            data: reportData.value.revenue_trend?.profit || [],
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4,
            fill: true,
            borderWidth: 3,
            pointRadius: 5,
            pointHoverRadius: 7,
            pointBackgroundColor: '#10b981',
            pointBorderColor: '#fff',
            pointBorderWidth: 2
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
          mode: 'index',
          intersect: false
        },
        plugins: {
          legend: {
            display: true,
            position: 'top',
            labels: {
              usePointStyle: true,
              padding: 15,
              font: {
                size: 12,
                weight: 600
              }
            }
          },
          tooltip: {
            mode: 'index',
            intersect: false,
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            padding: 12,
            borderColor: 'rgba(255, 255, 255, 0.1)',
            borderWidth: 1,
            titleFont: {
              size: 14,
              weight: 'bold'
            },
            bodyFont: {
              size: 13
            },
            callbacks: {
              label: function(context) {
                return `${context.dataset.label}: Rs. ${new Intl.NumberFormat('en-IN').format(context.raw)}`;
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
            title: {
              display: true,
              text: 'Amount (Rs.)',
              font: {
                size: 13,
                weight: 600
              }
            },
            ticks: {
              callback: function(value) {
                return 'Rs. ' + new Intl.NumberFormat('en-IN').format(value);
              },
              font: {
                size: 11
              }
            }
          },
          x: {
            grid: {
              display: false
            },
            ticks: {
              font: {
                size: 11
              }
            }
          }
        }
      }
    })
  }

  // Plant Distribution Chart
  if (plantChart.value) {
    const ctx = plantChart.value.getContext('2d')
    const plants = reportData.value.plant_stats || []
    chartInstances.plant = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: plants.map(p => p.name),
        datasets: [{
          data: plants.map(p => p.total_trips),
          backgroundColor: [
            '#ef4444', '#f97316', '#eab308', '#22c55e',
            '#3b82f6', '#8b5cf6', '#ec4899', '#10b981',
            '#6366f1', '#f59e0b'
          ],
          borderColor: '#ffffff',
          borderWidth: 3,
          hoverOffset: 15
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: true,
            position: 'right',
            labels: {
              usePointStyle: true,
              padding: 15,
              font: {
                size: 12,
                weight: 600
              }
            }
          },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            padding: 12,
            borderColor: 'rgba(255, 255, 255, 0.1)',
            borderWidth: 1,
            titleFont: {
              size: 14,
              weight: 'bold'
            },
            bodyFont: {
              size: 13
            },
            callbacks: {
              label: function(context) {
                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                const percentage = ((context.raw / total) * 100).toFixed(1);
                return `${context.label}: ${context.raw} trips (${percentage}%)`;
              }
            }
          }
        }
      }
    })
  }

  // Daily Income Chart
  if (dailyIncomeChart.value) {
    const ctx = dailyIncomeChart.value.getContext('2d')
    chartInstances.daily = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: reportData.value.daily_income?.labels || [],
        datasets: [{
          label: 'Daily Income',
          data: reportData.value.daily_income?.data || [],
          backgroundColor: 'rgba(16, 185, 129, 0.8)',
          borderColor: '#10b981',
          borderWidth: 2,
          borderRadius: 8,
          hoverBackgroundColor: 'rgba(16, 185, 129, 1)'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: true,
            labels: {
              usePointStyle: true,
              padding: 15,
              font: {
                size: 12,
                weight: 600
              }
            }
          },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            padding: 12,
            borderColor: 'rgba(255, 255, 255, 0.1)',
            borderWidth: 1,
            titleFont: {
              size: 14,
              weight: 'bold'
            },
            bodyFont: {
              size: 13
            },
            callbacks: {
              label: function(context) {
                return `${context.dataset.label}: Rs. ${new Intl.NumberFormat('en-IN').format(context.raw)}`;
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
            title: {
              display: true,
              text: 'Income (Rs.)',
              font: {
                size: 13,
                weight: 600
              }
            },
            ticks: {
              callback: function(value) {
                return 'Rs. ' + new Intl.NumberFormat('en-IN').format(value);
              },
              font: {
                size: 11
              }
            }
          },
          x: {
            grid: {
              display: false
            },
            ticks: {
              font: {
                size: 11
              }
            }
          }
        }
      }
    })
  }
}

// Watchers
watch(() => filters.value.search, () => {
  // Search is handled by computed properties
})

// Lifecycle
onMounted(() => {
  // Load Chart.js
  if (!window.Chart) {
    const script = document.createElement('script')
    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js'
    script.onload = () => {
      nextTick(() => {
        initCharts()
      })
    }
    document.head.appendChild(script)
  } else {
    nextTick(() => {
      initCharts()
    })
  }
})
</script>

<style scoped>
@keyframes fade-in {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes slide-up {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.6s ease-out;
}

.animate-slide-up {
  animation: slide-up 0.6s ease-out;
  animation-fill-mode: both;
}
</style>