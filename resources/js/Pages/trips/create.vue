<template>
  <AuthenticatedLayout>
    <!-- Subtle Animated Background -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none opacity-30">
      <div class="absolute top-0 left-0 w-96 h-96 bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl animate-float"></div>
      <div class="absolute top-0 right-0 w-96 h-96 bg-purple-100 rounded-full mix-blend-multiply filter blur-3xl animate-float-delayed"></div>
      <div class="absolute bottom-0 left-1/2 w-96 h-96 bg-indigo-100 rounded-full mix-blend-multiply filter blur-3xl animate-float-slow"></div>
    </div>

    <div class="container mx-auto px-4 py-8 relative z-10">
      <!-- Modern Header -->
      <div class="flex justify-between items-center mb-8 animate-fade-in-down">
        <div>
          <h1 class="text-4xl font-extrabold text-gray-900 mb-2 tracking-tight">
            Create New Trip(s)
          </h1>
          <p class="text-gray-500 flex items-center">
            <i class="fas fa-route mr-2 text-blue-500"></i>
            Manage your delivery trips efficiently
          </p>
        </div>
        <Link :href="route('trips.index')" 
          class="group px-6 py-3 bg-white text-gray-700 font-semibold rounded-xl shadow-md hover:shadow-xl border-2 border-gray-200 hover:border-gray-300 transition-all duration-300 hover:-translate-y-1">
          <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform duration-300 inline-block"></i>
          Back to Trips
        </Link>
      </div>

      <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden animate-fade-in">
        <!-- Driver Balance Alert with Modern Design -->
        <transition name="slide-fade">
          <div v-if="driverBalance !== 0 && form.driver_id" class="m-6 mb-0">
            <div :class="balanceAlertClass" class="p-6 rounded-xl shadow-lg border-l-4 animate-slide-in-right">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                  <div class="p-3 rounded-full" :class="balanceIconBgClass">
                    <i :class="balanceIconClass" class="text-2xl"></i>
                  </div>
                  <div>
                    <h3 class="font-bold text-lg text-gray-900">{{ balanceTitle }}</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ balanceMessage }}</p>
                  </div>
                </div>
                <div class="text-right">
                  <div class="text-3xl font-bold" :class="balanceAmountClass">
                    {{ formatCurrency(Math.abs(driverBalance)) }}
                  </div>
                  <div class="text-xs text-gray-500 mt-1">Current Balance</div>
                </div>
              </div>
            </div>
          </div>
        </transition>

        <form @submit.prevent="submitForm" class="p-6">
          <!-- Main Form Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Tipper Selection -->
            <div class="form-field" style="animation-delay: 0.05s">
              <label class="form-label">
                <i class="fas fa-truck text-blue-500 mr-2"></i>
                Tipper Number *
              </label>
              <select
                v-model="form.tipper_number"
                @change="onTipperChange"
                class="form-input"
                :class="{ 'border-red-400 focus:border-red-500 focus:ring-red-200': form.errors.tipper_number }"
              >
                <option value="">Select Tipper</option>
                <option v-for="tipper in tippers" :key="tipper.tipper_number" :value="tipper.tipper_number">
                  {{ tipper.tipper_number }} - {{ tipper.size }} Ton
                </option>
              </select>
              <transition name="fade">
                <p v-if="form.errors.tipper_number" class="error-message">
                  <i class="fas fa-exclamation-circle mr-1"></i>
                  {{ form.errors.tipper_number }}
                </p>
              </transition>
            </div>

            <!-- Driver Selection -->
            <div class="form-field" style="animation-delay: 0.1s">
              <label class="form-label">
                <i class="fas fa-user text-green-500 mr-2"></i>
                Driver *
              </label>
              <select
                v-model="form.driver_id"
                @change="onDriverChange"
                class="form-input"
                :class="{ 'border-red-400 focus:border-red-500 focus:ring-red-200': form.errors.driver_id }"
              >
                <option value="">Select Driver</option>
                <option
                  v-for="driver in drivers"
                  :key="driver.id"
                  :value="driver.id"
                  :class="{ 'bg-blue-50 text-blue-700 font-semibold': isPreferredDriver(driver) }"
                >
                  {{ driver.name }} - {{ driver.phone }}
                  <span v-if="driver.current_balance !== undefined">
                    ({{ driver.current_balance < 0 ? 'Owes: Rs ' + Math.abs(driver.current_balance).toFixed(2) : 'Balance: Rs 0.00' }})
                  </span>
                </option>
              </select>
              <transition name="fade">
                <p v-if="form.errors.driver_id" class="error-message">
                  <i class="fas fa-exclamation-circle mr-1"></i>
                  {{ form.errors.driver_id }}
                </p>
              </transition>
            </div>

            <!-- Plant Selection -->
            <div class="form-field" style="animation-delay: 0.15s">
              <label class="form-label">
                <i class="fas fa-industry text-purple-500 mr-2"></i>
                Plant *
              </label>
              <select
                v-model="form.plant_id"
                class="form-input"
                :class="{ 'border-red-400 focus:border-red-500 focus:ring-red-200': form.errors.plant_id }"
              >
                <option value="">Select Plant</option>
                <option v-for="plant in plants" :key="plant.id" :value="plant.id">
                  {{ plant.name }} - {{ plant.location }}
                </option>
              </select>
              <transition name="fade">
                <p v-if="form.errors.plant_id" class="error-message">
                  <i class="fas fa-exclamation-circle mr-1"></i>
                  {{ form.errors.plant_id }}
                </p>
              </transition>
            </div>

            <!-- Total Trips -->
            <div class="form-field" style="animation-delay: 0.2s">
              <label class="form-label">
                <i class="fas fa-list-ol text-orange-500 mr-2"></i>
                Total Trips *
              </label>
              <select
                v-model="form.total_trips"
                class="form-input"
                :class="{ 'border-red-400 focus:border-red-500 focus:ring-red-200': form.errors.total_trips }"
              >
                <option value="">Select Number of Trips</option>
                <option v-for="n in 20" :key="n" :value="n">{{ n }} Trip{{ n > 1 ? 's' : '' }}</option>
              </select>
              <transition name="fade">
                <p v-if="form.errors.total_trips" class="error-message">
                  <i class="fas fa-exclamation-circle mr-1"></i>
                  {{ form.errors.total_trips }}
                </p>
              </transition>
            </div>

            <!-- Delivery Date -->
            <div class="form-field" style="animation-delay: 0.25s">
              <label class="form-label">
                <i class="fas fa-calendar text-indigo-500 mr-2"></i>
                Delivery Date *
              </label>
              <input
                v-model="form.delivery_date"
                type="date"
                class="form-input"
                :class="{ 'border-red-400 focus:border-red-500 focus:ring-red-200': form.errors.delivery_date }"
              />
              <transition name="fade">
                <p v-if="form.errors.delivery_date" class="error-message">
                  <i class="fas fa-exclamation-circle mr-1"></i>
                  {{ form.errors.delivery_date }}
                </p>
              </transition>
            </div>

            <!-- Delivery Time -->
            <div class="form-field" style="animation-delay: 0.3s">
              <label class="form-label">
                <i class="fas fa-clock text-teal-500 mr-2"></i>
                Delivery Time
              </label>
              <input
                v-model="form.delivery_time"
                type="time"
                class="form-input"
              />
            </div>

            <!-- Trip Amount -->
            <div class="form-field" style="animation-delay: 0.35s">
              <label class="form-label">
                <i class="fas fa-dollar-sign text-green-500 mr-2"></i>
                Trip Amount (Per Trip) *
              </label>
              <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-sm"></span>
                <input
                  v-model="form.trip_amount_per_trip"
                  type="number"
                  step="0.01"
                  min="0"
                  class="form-input pl-12"
                  :class="{ 'border-red-400 focus:border-red-500 focus:ring-red-200': form.errors.trip_amount_per_trip }"
                  placeholder="Rs:0.00"
                />
              </div>
              <transition name="fade">
                <p v-if="form.errors.trip_amount_per_trip" class="error-message">
                  <i class="fas fa-exclamation-circle mr-1"></i>
                  {{ form.errors.trip_amount_per_trip }}
                </p>
              </transition>
            </div>

            <!-- Driver Salary -->
            <div class="form-field" style="animation-delay: 0.4s">
              <label class="form-label">
                <i class="fas fa-wallet text-red-500 mr-2"></i>
                Driver Salary (Per Trip) *
              </label>
              <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-sm"></span>
                <input
                  v-model="form.driver_salary_per_trip"
                  type="number"
                  step="0.01"
                  min="0"
                  class="form-input pl-12"
                  :class="{ 'border-red-400 focus:border-red-500 focus:ring-red-200': form.errors.driver_salary_per_trip }"
                  placeholder="RS: 0.00"
                />
              </div>
              <transition name="fade">
                <p v-if="form.errors.driver_salary_per_trip" class="error-message">
                  <i class="fas fa-exclamation-circle mr-1"></i>
                  {{ form.errors.driver_salary_per_trip }}
                </p>
              </transition>
            </div>
          </div>

          <!-- Advanced Payment Section - Redesigned -->
          <transition name="expand">
            <div v-if="form.driver_id && form.driver_salary_per_trip && form.total_trips" class="mb-8">
              <div class="bg-gradient-to-br from-amber-50 via-yellow-50 to-orange-50 rounded-2xl p-6 border-2 border-amber-200 shadow-lg animate-scale-in">
                <div class="flex items-center justify-between mb-6">
                  <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <div class="p-2 bg-amber-100 rounded-lg mr-3">
                      <i class="fas fa-hand-holding-usd text-amber-600 text-xl"></i>
                    </div>
                    Advanced Payment Management
                  </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                  <!-- Give Advance -->
                  <div class="bg-white rounded-xl p-5 shadow-sm border border-amber-100 hover:shadow-md transition-shadow duration-300">
                    <label class="block text-sm font-bold text-gray-800 mb-3">
                      <i class="fas fa-arrow-up text-amber-500 mr-2"></i>
                      Give Advance Payment
                    </label>
                    <div class="relative">
                      <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-sm">Rs</span>
                      <input
                        v-model="form.advance_amount"
                        type="number"
                        step="0.01"
                        min="0"
                        class="w-full pl-11 pr-3 py-2.5 border-2 border-amber-200 rounded-lg focus:outline-none focus:border-amber-400 focus:ring-4 focus:ring-amber-100 transition-all duration-300 text-gray-900 font-semibold"
                        placeholder="0.00"
                      />
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Any amount can be given</p>
                  </div>

                  <!-- Deduct from Salary -->
                  <div class="bg-white rounded-xl p-5 shadow-sm border border-red-100 hover:shadow-md transition-shadow duration-300">
                    <label class="block text-sm font-bold text-gray-800 mb-3">
                      <i class="fas fa-arrow-down text-red-500 mr-2"></i>
                      Deduct from Salary
                      <span v-if="driverBalance < 0" class="block text-xs text-red-600 font-semibold mt-1">
                        Owes: {{ formatCurrency(Math.abs(driverBalance)) }}
                      </span>
                    </label>
                    <div class="relative">
                      <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-sm">Rs</span>
                      <input
                        v-model="form.deduction_amount"
                        type="number"
                        step="0.01"
                        min="0"
                        :max="Math.min(totalDriverSalary, Math.abs(driverBalance))"
                        class="w-full pl-11 pr-3 py-2.5 border-2 border-red-200 rounded-lg focus:outline-none focus:border-red-400 focus:ring-4 focus:ring-red-100 transition-all duration-300 text-gray-900 font-semibold"
                        placeholder="0.00"
                      />
                    </div>
                    <div class="flex gap-2 mt-3" v-if="driverBalance < 0">
                      <button
                        type="button"
                        @click="setDeductionFull"
                        class="flex-1 text-xs px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors duration-300 font-semibold"
                      >
                        Full
                      </button>
                      <button
                        type="button"
                        @click="setDeductionHalf"
                        class="flex-1 text-xs px-3 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors duration-300 font-semibold"
                      >
                        Half
                      </button>
                    </div>
                  </div>

                  <!-- Payment Notes -->
                  <div class="bg-white rounded-xl p-5 shadow-sm border border-blue-100 hover:shadow-md transition-shadow duration-300">
                    <label class="block text-sm font-bold text-gray-800 mb-3">
                      <i class="fas fa-sticky-note text-blue-500 mr-2"></i>
                      Payment Notes
                    </label>
                    <textarea
                      v-model="form.payment_notes"
                      rows="3"
                      class="w-full px-3 py-2.5 border-2 border-blue-200 rounded-lg focus:outline-none focus:border-blue-400 focus:ring-4 focus:ring-blue-100 transition-all duration-300 resize-none text-sm"
                      placeholder="Optional notes..."
                    ></textarea>
                  </div>
                </div>

                <!-- Payment Summary Cards -->
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                  <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-green-400">
                    <div class="text-xs text-gray-600 mb-1">Total Salary</div>
                    <div class="text-xl font-bold text-green-600">{{ formatCurrency(totalDriverSalary) }}</div>
                  </div>
                  <div v-if="form.advance_amount > 0" class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-amber-400">
                    <div class="text-xs text-gray-600 mb-1">+ Advance</div>
                    <div class="text-xl font-bold text-amber-600">{{ formatCurrency(parseFloat(form.advance_amount || 0)) }}</div>
                  </div>
                  <div v-if="form.deduction_amount > 0" class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-red-400">
                    <div class="text-xs text-gray-600 mb-1">- Deduction</div>
                    <div class="text-xl font-bold text-red-600">{{ formatCurrency(parseFloat(form.deduction_amount || 0)) }}</div>
                  </div>
                  <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-blue-400">
                    <div class="text-xs text-gray-600 mb-1">Actually Paid</div>
                    <div class="text-xl font-bold text-blue-600">{{ formatCurrency(actuallyPaidAmount) }}</div>
                  </div>
                  <div class="bg-white rounded-xl p-4 shadow-sm border-l-4" :class="newDriverBalance < 0 ? 'border-red-400' : 'border-green-400'">
                    <div class="text-xs text-gray-600 mb-1">New Balance</div>
                    <div class="text-xl font-bold" :class="newBalanceClass">{{ formatCurrency(Math.abs(newDriverBalance)) }}</div>
                    <div class="text-xs mt-1" :class="newBalanceTextClass">{{ newBalanceText }}</div>
                  </div>
                </div>
              </div>
            </div>
          </transition>

          <!-- Trip Calculations - Completely Redesigned -->
          <transition name="expand">
            <div v-if="form.trip_amount_per_trip && form.driver_salary_per_trip && form.total_trips" class="mb-8">
              <div class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 rounded-2xl p-6 border-2 border-blue-200 shadow-lg animate-scale-in">
                <div class="flex items-center justify-between mb-6">
                  <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg mr-3">
                      <i class="fas fa-calculator text-blue-600 text-xl"></i>
                    </div>
                    Trip Calculations Summary
                  </h3>
                  <div class="flex items-center space-x-2 bg-white px-4 py-2 rounded-lg shadow-sm">
                    <i class="fas fa-chart-line text-gray-500"></i>
                    <span class="text-sm font-semibold text-gray-700">{{ form.total_trips }} Trips</span>
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                  <!-- Per Trip Card -->
                  <div class="bg-white rounded-xl p-5 shadow-md border border-blue-100 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-4">
                      <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Per Trip</h4>
                      <div class="p-2 bg-blue-50 rounded-lg">
                        <i class="fas fa-truck text-blue-500"></i>
                      </div>
                    </div>
                    <div class="space-y-3">
                      <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Amount:</span>
                        <span class="text-base font-bold text-green-600">{{ formatCurrency(parseFloat(form.trip_amount_per_trip || 0)) }}</span>
                      </div>
                      <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Salary:</span>
                        <span class="text-base font-bold text-red-600">{{ formatCurrency(parseFloat(form.driver_salary_per_trip || 0)) }}</span>
                      </div>
                      <div class="flex justify-between items-center pt-3 border-t-2 border-gray-100">
                        <span class="text-sm font-bold text-gray-900">Income:</span>
                        <span :class="incomePerTripClass" class="text-lg font-bold">
                          {{ formatCurrency(incomePerTrip) }}
                        </span>
                      </div>
                    </div>
                  </div>

                  <!-- Total Card -->
                  <div class="bg-white rounded-xl p-5 shadow-md border border-green-100 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-4">
                      <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total</h4>
                      <div class="p-2 bg-green-50 rounded-lg">
                        <i class="fas fa-coins text-green-500"></i>
                      </div>
                    </div>
                    <div class="space-y-3">
                      <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Amount:</span>
                        <span class="text-base font-bold text-green-600">{{ formatCurrency(totalTripAmount) }}</span>
                      </div>
                      <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Salary:</span>
                        <span class="text-base font-bold text-red-600">{{ formatCurrency(totalDriverSalary) }}</span>
                      </div>
                      <div class="flex justify-between items-center pt-3 border-t-2 border-gray-100">
                        <span class="text-sm font-bold text-gray-900">Income:</span>
                        <span :class="totalIncomeClass" class="text-lg font-bold">
                          {{ formatCurrency(totalIncome) }}
                        </span>
                      </div>
                    </div>
                  </div>

                  <!-- Profit Analysis Card -->
                  <div class="bg-white rounded-xl p-5 shadow-md border border-purple-100 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-4">
                      <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Profit</h4>
                      <div class="p-2 bg-purple-50 rounded-lg">
                        <i class="fas fa-chart-pie text-purple-500"></i>
                      </div>
                    </div>
                    <div class="space-y-3">
                      <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Margin:</span>
                        <span :class="profitMarginClass" class="text-base font-bold">
                          {{ profitMargin.toFixed(1) }}%
                        </span>
                      </div>
                      <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Status:</span>
                        <span :class="statusBadgeClass" class="px-2 py-1 text-xs font-bold rounded-full">
                          {{ profitStatus }}
                        </span>
                      </div>
                      <div class="flex justify-between items-center pt-3 border-t-2 border-gray-100">
                        <span class="text-sm text-gray-600">Avg/Trip:</span>
                        <span :class="avgIncomeClass" class="text-base font-bold">
                          {{ formatCurrency(incomePerTrip) }}
                        </span>
                      </div>
                    </div>
                  </div>

                  <!-- Share Analysis Card -->
                  <div class="bg-white rounded-xl p-5 shadow-md border border-orange-100 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-4">
                      <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Share</h4>
                      <div class="p-2 bg-orange-50 rounded-lg">
                        <i class="fas fa-percentage text-orange-500"></i>
                      </div>
                    </div>
                    <div class="space-y-3">
                      <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Driver:</span>
                        <span class="text-base font-bold text-orange-600">{{ driverSharePercentage.toFixed(1) }}%</span>
                      </div>
                      <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Owner:</span>
                        <span class="text-base font-bold text-green-600">{{ ownerSharePercentage.toFixed(1) }}%</span>
                      </div>
                      <div class="flex justify-between items-center pt-3 border-t-2 border-gray-100">
                        <span class="text-sm text-gray-600">Trips:</span>
                        <span class="text-base font-bold text-blue-600">{{ form.total_trips }}</span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Visual Progress Bars -->
                <div class="bg-white rounded-xl p-5 shadow-sm">
                  <h4 class="text-sm font-bold text-gray-700 mb-4">Revenue Distribution</h4>
                  <div class="space-y-4">
                    <!-- Driver Share Bar -->
                    <div>
                      <div class="flex justify-between text-xs text-gray-600 mb-2">
                        <span>Driver Share</span>
                        <span class="font-semibold">{{ formatCurrency(totalDriverSalary) }} ({{ driverSharePercentage.toFixed(1) }}%)</span>
                      </div>
                      <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                        <div class="bg-gradient-to-r from-orange-400 to-red-500 h-3 rounded-full transition-all duration-1000 ease-out animate-progress-bar"
                          :style="{ width: driverSharePercentage + '%' }">
                        </div>
                      </div>
                    </div>
                    <!-- Owner Share Bar -->
                    <div>
                      <div class="flex justify-between text-xs text-gray-600 mb-2">
                        <span>Your Share</span>
                        <span class="font-semibold">{{ formatCurrency(totalIncome) }} ({{ ownerSharePercentage.toFixed(1) }}%)</span>
                      </div>
                      <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-400 to-emerald-500 h-3 rounded-full transition-all duration-1000 ease-out animate-progress-bar"
                          :style="{ width: ownerSharePercentage + '%' }">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </transition>

          <!-- Action Buttons -->
          <div class="flex justify-end space-x-4">
            <Link :href="route('trips.index')" 
              class="px-8 py-3 border-2 border-gray-300 rounded-xl text-gray-700 font-semibold hover:bg-gray-50 hover:border-gray-400 transition-all duration-300 shadow-sm hover:shadow-md">
              <i class="fas fa-times mr-2"></i>
              Cancel
            </Link>
            <button
              type="submit"
              :disabled="form.processing || !canSubmit"
              class="px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl hover:from-blue-700 hover:to-blue-800 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:shadow-lg transition-all duration-300 transform hover:-translate-y-0.5 disabled:transform-none"
            >
              <i v-if="form.processing" class="fas fa-spinner fa-spin mr-2"></i>
              <i v-else class="fas fa-check-circle mr-2"></i>
              {{ form.processing ? 'Creating Trips...' : `Create ${form.total_trips || 0} Trip${(form.total_trips || 0) !== 1 ? 's' : ''}` }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { computed, watch, ref, onMounted } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  tippers: Array,
  drivers: Array,
  plants: Array
})

const driverBalance = ref(0)

const form = useForm({
  tipper_number: '',
  driver_id: '',
  plant_id: '',
  delivery_date: new Date().toISOString().split('T')[0],
  delivery_time: '',
  trip_amount_per_trip: '',
  driver_salary_per_trip: '',
  total_trips: '',
  advance_amount: '',
  deduction_amount: '',
  payment_notes: ''
})

// Calculations
const incomePerTrip = computed(() => {
  return parseFloat(form.trip_amount_per_trip || 0) - parseFloat(form.driver_salary_per_trip || 0)
})

const totalTripAmount = computed(() => {
  return parseFloat(form.trip_amount_per_trip || 0) * parseInt(form.total_trips || 0)
})

const totalDriverSalary = computed(() => {
  return parseFloat(form.driver_salary_per_trip || 0) * parseInt(form.total_trips || 0)
})

const totalIncome = computed(() => {
  return totalTripAmount.value - totalDriverSalary.value
})

const profitMargin = computed(() => {
  if (totalTripAmount.value === 0) return 0
  return (totalIncome.value / totalTripAmount.value) * 100
})

const driverSharePercentage = computed(() => {
  if (totalTripAmount.value === 0) return 0
  return (totalDriverSalary.value / totalTripAmount.value) * 100
})

const ownerSharePercentage = computed(() => {
  if (totalTripAmount.value === 0) return 0
  return (totalIncome.value / totalTripAmount.value) * 100
})

const profitStatus = computed(() => {
  if (totalIncome.value < 0) return 'Loss'
  if (profitMargin.value < 10) return 'Low Profit'
  if (profitMargin.value < 25) return 'Good Profit'
  return 'High Profit'
})

// Advanced payment calculations
const actuallyPaidAmount = computed(() => {
  const salary = totalDriverSalary.value
  const advance = parseFloat(form.advance_amount || 0)
  const deduction = parseFloat(form.deduction_amount || 0)
  return salary + advance - deduction
})

const newDriverBalance = computed(() => {
  const current = driverBalance.value
  const advance = parseFloat(form.advance_amount || 0)
  const deduction = parseFloat(form.deduction_amount || 0)
  return current - advance + deduction
})

const newBalanceText = computed(() => {
  if (newDriverBalance.value < 0) return 'Will owe'
  if (newDriverBalance.value > 0) return 'Will have credit'
  return 'Balanced'
})

const newBalanceClass = computed(() => {
  return newDriverBalance.value < 0 ? 'text-red-600' : 
         newDriverBalance.value > 0 ? 'text-green-600' : 'text-gray-600'
})

const newBalanceTextClass = computed(() => {
  return newDriverBalance.value < 0 ? 'text-red-500' : 
         newDriverBalance.value > 0 ? 'text-green-500' : 'text-gray-500'
})

const balanceAlertClass = computed(() => {
  return driverBalance.value < 0 
    ? 'bg-red-50 border-red-400' 
    : driverBalance.value > 0
    ? 'bg-green-50 border-green-400'
    : 'bg-gray-50 border-gray-400'
})

const balanceIconClass = computed(() => {
  return driverBalance.value < 0 
    ? 'fas fa-exclamation-triangle text-red-500' 
    : driverBalance.value > 0
    ? 'fas fa-check-circle text-green-500'
    : 'fas fa-info-circle text-gray-500'
})

const balanceIconBgClass = computed(() => {
  return driverBalance.value < 0 
    ? 'bg-red-100' 
    : driverBalance.value > 0
    ? 'bg-green-100'
    : 'bg-gray-100'
})

const balanceAmountClass = computed(() => {
  return driverBalance.value < 0 
    ? 'text-red-600' 
    : driverBalance.value > 0
    ? 'text-green-600'
    : 'text-gray-600'
})

const balanceTitle = computed(() => {
  if (driverBalance.value < 0) 
    return `Driver Owes: ${formatCurrency(Math.abs(driverBalance.value))}`
  if (driverBalance.value > 0) 
    return `Driver Has Credit: ${formatCurrency(driverBalance.value)}`
  return 'Driver Balance: Rs 0.00'
})

const balanceMessage = computed(() => {
  return driverBalance.value < 0 
    ? 'Driver has outstanding advance. Consider deducting from this trip.'
    : driverBalance.value > 0
    ? 'Driver has positive balance (credit).'
    : 'Driver has no advance balance.'
})

// Driver helpers
const preferredDriver = computed(() => {
  if (!form.tipper_number) return null
  return props.drivers.find(driver => driver.tipper_number === form.tipper_number)
})

const selectedDriver = computed(() => {
  if (!form.driver_id) return null
  return props.drivers.find(driver => driver.id === form.driver_id)
})

const isPreferredDriver = (driver) => {
  return form.tipper_number && driver.tipper_number === form.tipper_number
}

// Styling classes
const incomePerTripClass = computed(() => incomePerTrip.value >= 0 ? 'text-green-600' : 'text-red-600')
const totalIncomeClass = computed(() => totalIncome.value >= 0 ? 'text-green-600' : 'text-red-600')
const profitMarginClass = computed(() => {
  if (profitMargin.value < 0) return 'text-red-600'
  if (profitMargin.value < 10) return 'text-yellow-600'
  return 'text-green-600'
})
const statusBadgeClass = computed(() => {
  if (totalIncome.value < 0) return 'bg-red-500 text-white'
  if (profitMargin.value < 10) return 'bg-yellow-500 text-white'
  if (profitMargin.value < 25) return 'bg-green-500 text-white'
  return 'bg-blue-500 text-white'
})
const avgIncomeClass = computed(() => incomePerTrip.value >= 0 ? 'text-green-600' : 'text-red-600')

const canSubmit = computed(() => {
  return form.tipper_number &&
         form.driver_id &&
         form.plant_id &&
         form.delivery_date &&
         form.trip_amount_per_trip &&
         form.driver_salary_per_trip &&
         form.total_trips
})

// Methods
const formatCurrency = (value) => {
  return 'Rs ' + parseFloat(value).toFixed(2)
}

const onTipperChange = () => {
  if (preferredDriver.value) {
    form.driver_id = preferredDriver.value.id
    loadDriverBalance()
  } else {
    form.driver_id = ''
  }
}

const onDriverChange = () => {
  loadDriverBalance()
}

const loadDriverBalance = async () => {
  if (!form.driver_id) {
    driverBalance.value = 0
    return
  }
  
  try {
    const response = await fetch(`/api/drivers/${form.driver_id}/balance`)
    const data = await response.json()
    driverBalance.value = data.balance || 0
  } catch (error) {
    console.error('Failed to load driver balance:', error)
    driverBalance.value = 0
  }
}

const setDeductionFull = () => {
  const maxDeduction = Math.min(totalDriverSalary.value, Math.abs(driverBalance.value))
  form.deduction_amount = maxDeduction.toFixed(2)
}

const setDeductionHalf = () => {
  const maxDeduction = Math.min(totalDriverSalary.value / 2, Math.abs(driverBalance.value))
  form.deduction_amount = maxDeduction.toFixed(2)
}

const submitForm = () => {
  if (!canSubmit.value) return

  form.post(route('trips.store'), {
    onSuccess: () => {
      console.log('Trips created successfully')
    },
    onError: (errors) => {
      console.error('Form submission errors:', errors)
    }
  })
}

onMounted(() => {
  const firstField = document.querySelector('select')
  if (firstField) firstField.focus()
})
</script>

<style scoped>
/* Keyframe Animations */
@keyframes float {
  0%, 100% { transform: translate(0, 0) rotate(0deg); }
  33% { transform: translate(30px, -30px) rotate(3deg); }
  66% { transform: translate(-20px, 20px) rotate(-3deg); }
}

@keyframes float-delayed {
  0%, 100% { transform: translate(0, 0) rotate(0deg); }
  33% { transform: translate(-30px, 30px) rotate(-3deg); }
  66% { transform: translate(20px, -20px) rotate(3deg); }
}

@keyframes float-slow {
  0%, 100% { transform: translate(0, 0) scale(1); }
  50% { transform: translate(0, -20px) scale(1.05); }
}

@keyframes fade-in-down {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fade-in {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slide-in-right {
  from {
    opacity: 0;
    transform: translateX(30px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes scale-in {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

@keyframes progress-bar {
  from { width: 0%; }
}

/* Animation Classes */
.animate-float {
  animation: float 20s ease-in-out infinite;
}

.animate-float-delayed {
  animation: float-delayed 25s ease-in-out infinite;
}

.animate-float-slow {
  animation: float-slow 30s ease-in-out infinite;
}

.animate-fade-in-down {
  animation: fade-in-down 0.6s ease-out;
}

.animate-fade-in {
  animation: fade-in 0.8s ease-out;
}

.animate-slide-in-right {
  animation: slide-in-right 0.5s ease-out;
}

.animate-scale-in {
  animation: scale-in 0.5s ease-out;
}

.animate-progress-bar {
  animation: progress-bar 1s ease-out;
}

/* Form Field Styling */
.form-field {
  animation: fade-in 0.6s ease-out backwards;
}

.form-label {
  @apply block text-sm font-semibold text-gray-700 mb-2 flex items-center;
}

.form-input {
  @apply w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300 bg-white;
}

.form-input:hover {
  @apply border-gray-300;
}

.error-message {
  @apply mt-2 text-sm text-red-600 flex items-center;
}

/* Vue Transitions */
.slide-fade-enter-active {
  transition: all 0.5s ease-out;
}

.slide-fade-leave-active {
  transition: all 0.3s ease-in;
}

.slide-fade-enter-from {
  transform: translateY(-10px);
  opacity: 0;
}

.slide-fade-leave-to {
  transform: translateY(10px);
  opacity: 0;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.expand-enter-active {
  transition: all 0.5s ease-out;
  overflow: hidden;
}

.expand-leave-active {
  transition: all 0.3s ease-in;
  overflow: hidden;
}

.expand-enter-from {
  max-height: 0;
  opacity: 0;
  transform: translateY(-20px);
}

.expand-leave-to {
  max-height: 0;
  opacity: 0;
  transform: translateY(-20px);
}

.expand-enter-to, .expand-leave-from {
  max-height: 2000px;
  opacity: 1;
  transform: translateY(0);
}

/* Container */
.container {
  max-width: 1400px;
}

/* Custom Scrollbar */
::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: linear-gradient(180deg, #3b82f6, #2563eb);
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(180deg, #2563eb, #1d4ed8);
}

/* Button Hover Effects */
button:hover:not(:disabled) {
  transform: translateY(-2px);
}

button:active:not(:disabled) {
  transform: translateY(0);
}
</style>