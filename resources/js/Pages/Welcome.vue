<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

const canvas = ref(null);
let animationId = null;
let lastFrameTime = null;
const ANIMATION_TIMEOUT = 1000; // 1 second timeout to detect animation stall

onMounted(() => {
    if (!canvas.value) return;

    const ctx = canvas.value.getContext('2d');
    canvas.value.width = window.innerWidth;
    canvas.value.height = window.innerHeight;

    // Enhanced delivery system
    const points = [];
    const routes = [];
    const trucks = [];
    const packages = [];
    const cities = [];
    const numPoints = 15;
    const numTrucks = 6;
    const numCities = 5;

    // Create city hubs (larger delivery centers)
    for (let i = 0; i < numCities; i++) {
        cities.push({
            x: (canvas.value.width / (numCities + 1)) * (i + 1),
            y: canvas.value.height / 2 + (Math.random() - 0.5) * 300,
            radius: 15,
            pulse: Math.random() * Math.PI * 2,
            name: ['Plant-A', 'Plant-B', 'Plant-C', 'Plant-D', 'Plant-E'][i],
            packages: Math.floor(Math.random() * 50) + 20,
            active: true
        });
    }

    // Create delivery points
    for (let i = 0; i < numPoints; i++) {
        points.push({
            x: Math.random() * canvas.value.width,
            y: Math.random() * canvas.value.height,
            radius: 5,
            pulse: Math.random() * Math.PI * 2,
            delivered: false,
            priority: Math.random() > 0.7,
            eta: Math.floor(Math.random() * 30) + 5
        });
    }

    // Create dynamic routes
    points.forEach((point, i) => {
        const nearestCity = cities.reduce((prev, curr) => {
            const prevDist = Math.hypot(prev.x - point.x, prev.y - point.y);
            const currDist = Math.hypot(curr.x - point.x, curr.y - point.y);
            return currDist < prevDist ? curr : prev;
        });

        routes.push({
            start: nearestCity,
            end: point,
            progress: Math.random(),
            active: Math.random() > 0.5,
            dataFlow: []
        });
    });

    // Connect cities
    for (let i = 0; i < cities.length - 1; i++) {
        routes.push({
            start: cities[i],
            end: cities[i + 1],
            progress: Math.random(),
            active: true,
            dataFlow: [],
            highway: true
        });
    }

    // Create enhanced trucks
    for (let i = 0; i < numTrucks; i++) {
        const startPoint = cities[Math.floor(Math.random() * cities.length)];
        trucks.push({
            x: startPoint.x,
            y: startPoint.y,
            targetIndex: Math.floor(Math.random() * points.length),
            speed: 1 + Math.random() * 1.5,
            size: 10,
            trail: [],
            color: `hsl(${180 + i * 30}, 70%, 50%)`,
            packages: Math.floor(Math.random() * 5) + 1,
            angle: 0,
            targetAngle: 0,
            delivering: false,
            routeHistory: []
        });
    }

    // Floating packages
    for (let i = 0; i < 30; i++) {
        packages.push({
            x: Math.random() * canvas.value.width,
            y: Math.random() * canvas.value.height,
            vx: (Math.random() - 0.5) * 0.3,
            vy: (Math.random() - 0.5) * 0.3,
            size: 3,
            opacity: Math.random() * 0.3 + 0.1,
            pulse: Math.random() * Math.PI * 2
        });
    }

    // Particles system
    const particles = [];
    const explosions = [];
    
    function createParticles(x, y, color = 'cyan') {
        for (let i = 0; i < 15; i++) {
            particles.push({
                x: x,
                y: y,
                vx: (Math.random() - 0.5) * 4,
                vy: (Math.random() - 0.5) * 4,
                life: 1,
                size: Math.random() * 4 + 2,
                color: color
            });
        }
    }

    function createExplosion(x, y) {
        explosions.push({
            x: x,
            y: y,
            radius: 0,
            maxRadius: 50,
            life: 1
        });
    }

    // Data packets flowing through routes
    function createDataFlow(route) {
        if (route.dataFlow.length < 3 && Math.random() > 0.98) {
            route.dataFlow.push({
                progress: 0,
                speed: 0.02 + Math.random() * 0.02,
                size: 3
            });
        }
    }

    let time = 0;

    function animate(currentTime) {
        if (!canvas.value || !ctx) {
            animationId = requestAnimationFrame(animate);
            return;
        }

        // Check for animation stall
        if (lastFrameTime && (currentTime - lastFrameTime > ANIMATION_TIMEOUT)) {
            console.warn('Animation stalled, restarting...');
            cancelAnimationFrame(animationId);
            animationId = requestAnimationFrame(animate);
            return;
        }
        lastFrameTime = currentTime;

        time += 0.01;
        
        // Create dynamic gradient background
        const bgGrd = ctx.createRadialGradient(
            canvas.value.width / 2, 
            canvas.value.height / 2, 
            0, 
            canvas.value.width / 2, 
            canvas.value.height / 2, 
            canvas.value.width
        );
        bgGrd.addColorStop(0, `rgba(15, 23, 42, ${0.95 + Math.sin(time) * 0.05})`);
        bgGrd.addColorStop(0.5, `rgba(30, 58, 138, ${0.5 + Math.sin(time * 0.5) * 0.1})`);
        bgGrd.addColorStop(1, 'rgba(15, 23, 42, 0.95)');
        
        ctx.fillStyle = bgGrd;
        ctx.fillRect(0, 0, canvas.value.width, canvas.value.height);

        // Animate floating packages in background
        packages.forEach(pkg => {
            pkg.x += pkg.vx;
            pkg.y += pkg.vy;
            pkg.pulse += 0.05;

            if (pkg.x < 0 || pkg.x > canvas.value.width) pkg.vx *= -1;
            if (pkg.y < 0 || pkg.y > canvas.value.height) pkg.vy *= -1;

            const pulseSize = Math.sin(pkg.pulse) * 1;
            ctx.fillStyle = `rgba(59, 130, 246, ${pkg.opacity})`;
            ctx.beginPath();
            ctx.arc(pkg.x, pkg.y, pkg.size + pulseSize, 0, Math.PI * 2);
            ctx.fill();
        });

        // Draw highway routes with animated dashes
        routes.forEach(route => {
            if (route.highway) {
                ctx.strokeStyle = 'rgba(59, 130, 246, 0.3)';
                ctx.lineWidth = 4;
                ctx.setLineDash([20, 10]);
                ctx.lineDashOffset = -time * 50;
                ctx.beginPath();
                ctx.moveTo(route.start.x, route.start.y);
                ctx.lineTo(route.end.x, route.end.y);
                ctx.stroke();
                ctx.setLineDash([]);
            }
        });

        // Draw connection lines with data flow
        routes.forEach(route => {
            createDataFlow(route);

            // Base route line
            ctx.strokeStyle = route.highway ? 'rgba(59, 130, 246, 0.2)' : 'rgba(59, 130, 246, 0.1)';
            ctx.lineWidth = route.highway ? 3 : 1.5;
            ctx.beginPath();
            ctx.moveTo(route.start.x, route.start.y);
            ctx.lineTo(route.end.x, route.end.y);
            ctx.stroke();

            // Animated route highlight
            if (route.active) {
                const dx = route.end.x - route.start.x;
                const dy = route.end.y - route.start.y;
                const length = Math.sqrt(dx * dx + dy * dy);
                
                for (let i = 0; i < 3; i++) {
                    const offset = (route.progress + i * 0.3) % 1;
                    const x = route.start.x + dx * offset;
                    const y = route.start.y + dy * offset;
                    
                    const grd = ctx.createRadialGradient(x, y, 0, x, y, 20);
                    grd.addColorStop(0, 'rgba(34, 211, 238, 0.8)');
                    grd.addColorStop(1, 'rgba(34, 211, 238, 0)');
                    
                    ctx.fillStyle = grd;
                    ctx.beginPath();
                    ctx.arc(x, y, 15, 0, Math.PI * 2);
                    ctx.fill();
                }

                route.progress += 0.005;
                if (route.progress >= 1) route.progress = 0;
            }

            // Data flow packets
            route.dataFlow.forEach((data, idx) => {
                data.progress += data.speed;
                
                const dx = route.end.x - route.start.x;
                const dy = route.end.y - route.start.y;
                const x = route.start.x + dx * data.progress;
                const y = route.start.y + dy * data.progress;

                // Glowing data packet
                const grd = ctx.createRadialGradient(x, y, 0, x, y, data.size * 3);
                grd.addColorStop(0, 'rgba(34, 211, 238, 1)');
                grd.addColorStop(0.5, 'rgba(59, 130, 246, 0.5)');
                grd.addColorStop(1, 'rgba(59, 130, 246, 0)');
                
                ctx.fillStyle = grd;
                ctx.beginPath();
                ctx.arc(x, y, data.size * 3, 0, Math.PI * 2);
                ctx.fill();

                ctx.fillStyle = '#fff';
                ctx.beginPath();
                ctx.arc(x, y, data.size, 0, Math.PI * 2);
                ctx.fill();

                if (data.progress >= 1) {
                    route.dataFlow.splice(idx, 1);
                }
            });
        });

        // Draw cities with enhanced effects
        cities.forEach(city => {
            city.pulse += 0.03;
            const pulseSize = Math.sin(city.pulse) * 3;

            // Outer rings
            for (let i = 3; i > 0; i--) {
                const grd = ctx.createRadialGradient(city.x, city.y, 0, city.x, city.y, city.radius + pulseSize + i * 15);
                grd.addColorStop(0, `rgba(59, 130, 246, ${0.2 / i})`);
                grd.addColorStop(1, 'rgba(59, 130, 246, 0)');
                ctx.fillStyle = grd;
                ctx.beginPath();
                ctx.arc(city.x, city.y, city.radius + pulseSize + i * 15, 0, Math.PI * 2);
                ctx.fill();
            }

            // City core
            const cityGrd = ctx.createRadialGradient(city.x, city.y, 0, city.x, city.y, city.radius);
            cityGrd.addColorStop(0, '#60a5fa');
            cityGrd.addColorStop(0.7, '#3b82f6');
            cityGrd.addColorStop(1, '#2563eb');
            ctx.fillStyle = cityGrd;
            ctx.beginPath();
            ctx.arc(city.x, city.y, city.radius + pulseSize, 0, Math.PI * 2);
            ctx.fill();

            // City highlight
            ctx.fillStyle = 'rgba(255, 255, 255, 0.6)';
            ctx.beginPath();
            ctx.arc(city.x - 3, city.y - 3, city.radius * 0.4, 0, Math.PI * 2);
            ctx.fill();

            // City name and stats
            ctx.fillStyle = '#fff';
            ctx.font = 'bold 11px Arial';
            ctx.textAlign = 'center';
            ctx.fillText(city.name, city.x, city.y - city.radius - 20);
            
            ctx.font = '9px Arial';
            ctx.fillStyle = '#22d3ee';
            ctx.fillText(`${city.packages} pkgs`, city.x, city.y - city.radius - 8);
        });

        // Draw delivery points with priority indicators
        points.forEach((point, index) => {
            point.pulse += 0.05;
            const pulseSize = Math.sin(point.pulse) * 2;

            // Priority ring
            if (point.priority && !point.delivered) {
                ctx.strokeStyle = 'rgba(239, 68, 68, 0.6)';
                ctx.lineWidth = 2;
                ctx.beginPath();
                ctx.arc(point.x, point.y, point.radius + pulseSize + 8, 0, Math.PI * 2);
                ctx.stroke();
            }

            // Outer glow
            const grd = ctx.createRadialGradient(point.x, point.y, 0, point.x, point.y, point.radius + pulseSize + 12);
            if (point.delivered) {
                grd.addColorStop(0, 'rgba(34, 197, 94, 0.5)');
                grd.addColorStop(1, 'rgba(34, 197, 94, 0)');
            } else if (point.priority) {
                grd.addColorStop(0, 'rgba(239, 68, 68, 0.4)');
                grd.addColorStop(1, 'rgba(239, 68, 68, 0)');
            } else {
                grd.addColorStop(0, 'rgba(59, 130, 246, 0.4)');
                grd.addColorStop(1, 'rgba(59, 130, 246, 0)');
            }
            ctx.fillStyle = grd;
            ctx.beginPath();
            ctx.arc(point.x, point.y, point.radius + pulseSize + 12, 0, Math.PI * 2);
            ctx.fill();

            // Main point
            ctx.fillStyle = point.delivered ? '#22c55e' : (point.priority ? '#ef4444' : '#3b82f6');
            ctx.beginPath();
            ctx.arc(point.x, point.y, point.radius + pulseSize, 0, Math.PI * 2);
            ctx.fill();

            // Inner highlight
            ctx.fillStyle = 'rgba(255, 255, 255, 0.7)';
            ctx.beginPath();
            ctx.arc(point.x - 1.5, point.y - 1.5, (point.radius + pulseSize) * 0.4, 0, Math.PI * 2);
            ctx.fill();

            // ETA display
            if (!point.delivered && point.eta < 10) {
                ctx.fillStyle = '#fbbf24';
                ctx.font = 'bold 9px Arial';
                ctx.textAlign = 'center';
                ctx.fillText(`${point.eta}m`, point.x, point.y + point.radius + 12);
            }
        });

        // Update and draw trucks with advanced graphics
        trucks.forEach((truck, idx) => {
            const target = points[truck.targetIndex];
            const dx = target.x - truck.x;
            const dy = target.y - truck.y;
            const dist = Math.sqrt(dx * dx + dy * dy);

            truck.targetAngle = Math.atan2(dy, dx);
            let angleDiff = truck.targetAngle - truck.angle;
            while (angleDiff > Math.PI) angleDiff -= Math.PI * 2;
            while (angleDiff < -Math.PI) angleDiff += Math.PI * 2;
            truck.angle += angleDiff * 0.1;

            if (dist > 3) {
                truck.x += Math.cos(truck.angle) * truck.speed;
                truck.y += Math.sin(truck.angle) * truck.speed;
                truck.delivering = false;

                // Enhanced trail
                truck.trail.push({ 
                    x: truck.x, 
                    y: truck.y, 
                    alpha: 1,
                    time: time
                });
                if (truck.trail.length > 30) truck.trail.shift();

                // Activate nearby routes
                routes.forEach(route => {
                    const distToRoute = pointToLineDistance(
                        truck.x, truck.y,
                        route.start.x, route.start.y,
                        route.end.x, route.end.y
                    );
                    if (distToRoute < 80) route.active = true;
                });
            } else {
                if (!target.delivered && !truck.delivering) {
                    truck.delivering = true;
                    target.delivered = true;
                    createExplosion(target.x, target.y);
                    createParticles(target.x, target.y, 'green');
                    truck.packages--;
                }
                
                if (truck.packages <= 0) {
                    const nearestCity = cities.reduce((prev, curr) => {
                        const prevDist = Math.hypot(prev.x - truck.x, prev.y - truck.y);
                        const currDist = Math.hypot(curr.x - truck.x, curr.y - truck.y);
                        return currDist < prevDist ? curr : prev;
                    });
                    truck.targetIndex = points.indexOf(nearestCity);
                    if (Math.hypot(nearestCity.x - truck.x, nearestCity.y - truck.y) < 20) {
                        truck.packages = Math.floor(Math.random() * 5) + 1;
                    }
                } else {
                    truck.targetIndex = Math.floor(Math.random() * points.length);
                }
            }

            // Draw enhanced trail with fade
            truck.trail.forEach((pos, i) => {
                const alpha = (i / truck.trail.length) * 0.6;
                const trailSize = (i / truck.trail.length) * 4 + 1;
                
                const grd = ctx.createRadialGradient(pos.x, pos.y, 0, pos.x, pos.y, trailSize * 2);
                grd.addColorStop(0, truck.color.replace('50%', `50%, ${alpha}`));
                grd.addColorStop(1, truck.color.replace('50%', `50%, 0`));
                
                ctx.fillStyle = grd;
                ctx.beginPath();
                ctx.arc(pos.x, pos.y, trailSize * 2, 0, Math.PI * 2);
                ctx.fill();
            });

            // Draw truck with 3D effect
            ctx.save();
            ctx.translate(truck.x, truck.y);
            ctx.rotate(truck.angle);

            // Truck shadow
            ctx.fillStyle = 'rgba(0, 0, 0, 0.3)';
            ctx.fillRect(-truck.size + 2, -truck.size/2 + 2, truck.size * 2.5, truck.size);

            // Truck body gradient
            const truckGrd = ctx.createLinearGradient(-truck.size, -truck.size/2, -truck.size, truck.size/2);
            truckGrd.addColorStop(0, truck.color.replace('50%', '60%'));
            truckGrd.addColorStop(0.5, truck.color);
            truckGrd.addColorStop(1, truck.color.replace('50%', '40%'));
            ctx.fillStyle = truckGrd;
            ctx.fillRect(-truck.size, -truck.size/2, truck.size * 2.5, truck.size);

            // Truck cab
            const cabGrd = ctx.createLinearGradient(truck.size * 0.8, -truck.size/2, truck.size * 0.8, truck.size/2);
            cabGrd.addColorStop(0, truck.color.replace('50%', '70%'));
            cabGrd.addColorStop(1, truck.color.replace('50%', '50%'));
            ctx.fillStyle = cabGrd;
            ctx.fillRect(truck.size * 0.8, -truck.size/2, truck.size * 0.7, truck.size);

            // Windows
            ctx.fillStyle = 'rgba(100, 200, 255, 0.6)';
            ctx.fillRect(truck.size * 0.9, -truck.size/3, truck.size * 0.4, truck.size * 0.25);

            // Truck highlight
            ctx.fillStyle = 'rgba(255, 255, 255, 0.4)';
            ctx.fillRect(-truck.size, -truck.size/2, truck.size * 2.5, truck.size/4);

            // Wheels
            ctx.fillStyle = '#1f2937';
            ctx.beginPath();
            ctx.arc(-truck.size * 0.3, truck.size/2, truck.size * 0.25, 0, Math.PI * 2);
            ctx.arc(truck.size * 0.3, truck.size/2, truck.size * 0.25, 0, Math.PI * 2);
            ctx.fill();

            // Headlights glow
            const lightGrd = ctx.createRadialGradient(truck.size * 1.5, 0, 0, truck.size * 1.5, 0, truck.size * 2);
            lightGrd.addColorStop(0, 'rgba(255, 255, 200, 0.4)');
            lightGrd.addColorStop(1, 'rgba(255, 255, 200, 0)');
            ctx.fillStyle = lightGrd;
            ctx.beginPath();
            ctx.arc(truck.size * 1.5, 0, truck.size * 2, 0, Math.PI * 2);
            ctx.fill();

            // Package indicator
            ctx.fillStyle = '#fbbf24';
            ctx.font = 'bold 8px Arial';
            ctx.textAlign = 'center';
            ctx.fillText(truck.packages, 0, -truck.size - 5);

            ctx.restore();

            // Truck outer glow
            ctx.shadowBlur = 20;
            ctx.shadowColor = truck.color;
            ctx.fillStyle = 'transparent';
            ctx.beginPath();
            ctx.arc(truck.x, truck.y, truck.size * 2, 0, Math.PI * 2);
            ctx.fill();
            ctx.shadowBlur = 0;
        });

        // Draw explosions
        explosions.forEach((exp, i) => {
            exp.radius += (exp.maxRadius - exp.radius) * 0.1;
            exp.life -= 0.02;

            if (exp.life <= 0) {
                explosions.splice(i, 1);
                return;
            }

            ctx.strokeStyle = `rgba(34, 197, 94, ${exp.life * 0.8})`;
            ctx.lineWidth = 3;
            ctx.beginPath();
            ctx.arc(exp.x, exp.y, exp.radius, 0, Math.PI * 2);
            ctx.stroke();

            ctx.strokeStyle = `rgba(34, 211, 238, ${exp.life * 0.5})`;
            ctx.lineWidth = 2;
            ctx.beginPath();
            ctx.arc(exp.x, exp.y, exp.radius * 0.7, 0, Math.PI * 2);
            ctx.stroke();
        });

        // Draw and update particles
        particles.forEach((p, i) => {
            p.x += p.vx;
            p.y += p.vy;
            p.vy += 0.1; // Gravity
            p.life -= 0.015;

            if (p.life <= 0) {
                particles.splice(i, 1);
                return;
            }

            const grd = ctx.createRadialGradient(p.x, p.y, 0, p.x, p.y, p.size);
            grd.addColorStop(0, p.color === 'green' ? `rgba(34, 197, 94, ${p.life})` : `rgba(34, 211, 238, ${p.life})`);
            grd.addColorStop(1, p.color === 'green' ? `rgba(34, 197, 94, 0)` : `rgba(34, 211, 238, 0)`);
            
            ctx.fillStyle = grd;
            ctx.beginPath();
            ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
            ctx.fill();
        });

        animationId = requestAnimationFrame(animate);
    }

    function pointToLineDistance(px, py, x1, y1, x2, y2) {
        const A = px - x1;
        const B = py - y1;
        const C = x2 - x1;
        const D = y2 - y1;

        const dot = A * C + B * D;
        const lenSq = C * C + D * D;
        let param = -1;

        if (lenSq !== 0) param = dot / lenSq;

        let xx, yy;

        if (param < 0) {
            xx = x1;
            yy = y1;
        } else if (param > 1) {
            xx = x2;
            yy = y2;
        } else {
            xx = x1 + param * C;
            yy = y1 + param * D;
        }

        const dx = px - xx;
        const dy = py - yy;
        return Math.sqrt(dx * dx + dy * dy);
    }

    // Start the animation
    animationId = requestAnimationFrame(animate);

    const handleResize = () => {
        if (canvas.value) {
            canvas.value.width = window.innerWidth;
            canvas.value.height = window.innerHeight;
            // Recalculate city positions to adapt to new canvas size
            cities.forEach((city, i) => {
                city.x = (canvas.value.width / (numCities + 1)) * (i + 1);
                city.y = canvas.value.height / 2 + (Math.random() - 0.5) * 300;
            });
            // Recalculate point positions
            points.forEach(point => {
                point.x = Math.random() * canvas.value.width;
                point.y = Math.random() * canvas.value.height;
            });
            // Recalculate package positions
            packages.forEach(pkg => {
                pkg.x = Math.random() * canvas.value.width;
                pkg.y = Math.random() * canvas.value.height;
            });
        }
    };

    window.addEventListener('resize', handleResize);

    // Cleanup on unmount
    onUnmounted(() => {
        window.removeEventListener('resize', handleResize);
        if (animationId) {
            cancelAnimationFrame(animationId);
            animationId = null;
        }
    });
});
</script>

<template>
    <Head title="KTS Transport Management" />
    <div class="relative bg-slate-900 text-white min-h-screen overflow-hidden">
        <!-- Animated Background Canvas -->
        <canvas 
            ref="canvas" 
            class="fixed inset-0 w-full h-full z-0"
            style="background: linear-gradient(to bottom right, #0f172a, #1e3a8a, #0f172a);"
        ></canvas>

        <!-- Overlay for better text readability -->
        <div class="fixed inset-0 bg-gradient-to-b from-slate-900/70 via-transparent to-slate-900/90 z-0 pointer-events-none"></div>

        <!-- Content -->
        <div class="relative z-10">
            <!-- Navigation -->
            <nav class="fixed top-0 w-full z-50 bg-slate-900/60 backdrop-blur-xl border-b border-white/10">
                <div class="max-w-7xl mx-auto px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="bg-gradient-to-br from-blue-500 to-cyan-400 p-2 rounded-lg shadow-lg shadow-blue-500/50">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-cyan-300 bg-clip-text text-transparent">KTS</h1>
                                <p class="text-xs text-gray-400">Transport Management</p>
                            </div>
                        </div>
                        
                        <div v-if="canLogin" class="flex items-center space-x-2">
                            <Link
                                v-if="$page.props.auth.user"
                                :href="route('dashboard')"
                                class="px-6 py-2 bg-gradient-to-r from-blue-600 to-cyan-500 rounded-lg font-semibold hover:from-blue-700 hover:to-cyan-600 transition-all duration-300 shadow-lg shadow-blue-500/50"
                            >
                                Dashboard
                            </Link>

                            <template v-else>
                                <Link
                                    :href="route('login')"
                                    class="px-6 py-2 text-gray-300 hover:text-white transition-colors duration-300"
                                >
                                    Log in
                                </Link>

                                <Link
                                    v-if="canRegister"
                                    :href="route('register')"
                                    class="px-6 py-2 bg-gradient-to-r from-blue-600 to-cyan-500 rounded-lg font-semibold hover:from-blue-700 hover:to-cyan-600 transition-all duration-300 shadow-lg shadow-blue-500/50"
                                >
                                    Get Started
                                </Link>
                            </template>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Hero Section -->
            <section class="pt-32 pb-20 px-6">
                <div class="max-w-7xl mx-auto">
                    <div class="text-center mb-16">
                        <h2 class="text-5xl md:text-7xl font-bold mb-6 bg-gradient-to-r from-blue-400 via-cyan-300 to-blue-400 bg-clip-text text-transparent drop-shadow-2xl">
                            Smart Transport Solutions
                        </h2>
                        <p class="text-xl md:text-2xl text-gray-200 max-w-3xl mx-auto mb-8 drop-shadow-lg">
                            Streamline your fleet management with real-time tracking, automated scheduling, and intelligent route optimization
                        </p>
                        <div class="flex flex-wrap gap-4 justify-center">
                            <button class="px-8 py-4 bg-gradient-to-r from-blue-600 to-cyan-500 rounded-lg font-semibold text-lg hover:from-blue-700 hover:to-cyan-600 transition-all duration-300 shadow-xl shadow-blue-500/50 hover:shadow-2xl hover:shadow-blue-500/70 hover:scale-105">
                                Start Free Trial
                            </button>
                            <button class="px-8 py-4 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg font-semibold text-lg hover:bg-white/20 transition-all duration-300">
                                Watch Demo
                            </button>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-20">
                        <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-xl p-6 text-center hover:bg-white/10 transition-all duration-300 hover:scale-105">
                            <div class="text-4xl font-bold text-cyan-400 mb-2">500+</div>
                            <div class="text-gray-300">Active Vehicles</div>
                        </div>
                        <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-xl p-6 text-center hover:bg-white/10 transition-all duration-300 hover:scale-105">
                            <div class="text-4xl font-bold text-blue-400 mb-2">50K+</div>
                            <div class="text-gray-300">Deliveries</div>
                        </div>
                        <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-xl p-6 text-center hover:bg-white/10 transition-all duration-300 hover:scale-105">
                            <div class="text-4xl font-bold text-cyan-400 mb-2">99.9%</div>
                            <div class="text-gray-300">Uptime</div>
                        </div>
                        <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-xl p-6 text-center hover:bg-white/10 transition-all duration-300 hover:scale-105">
                            <div class="text-4xl font-bold text-blue-400 mb-2">24/7</div>
                            <div class="text-gray-300">Support</div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Features Section -->
            <section class="py-20 px-6 bg-black/20 backdrop-blur-sm">
                <div class="max-w-7xl mx-auto">
                    <h3 class="text-4xl font-bold text-center mb-16 bg-gradient-to-r from-blue-400 to-cyan-300 bg-clip-text text-transparent">
                        Powerful Features
                    </h3>
                    
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <!-- Feature Cards -->
                        <div class="bg-gradient-to-br from-blue-900/40 to-slate-900/40 backdrop-blur-md border border-white/10 rounded-2xl p-8 hover:border-cyan-500/50 transition-all duration-300 hover:shadow-xl hover:shadow-cyan-500/20 group">
                            <div class="bg-gradient-to-br from-blue-500 to-cyan-400 w-14 h-14 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg shadow-blue-500/50">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <h4 class="text-xl font-bold mb-3 text-white">Real-Time Tracking</h4>
                            <p class="text-gray-300">Monitor your entire fleet in real-time with GPS tracking and live location updates</p>
                        </div>

                        <div class="bg-gradient-to-br from-blue-900/40 to-slate-900/40 backdrop-blur-md border border-white/10 rounded-2xl p-8 hover:border-cyan-500/50 transition-all duration-300 hover:shadow-xl hover:shadow-cyan-500/20 group">
                            <div class="bg-gradient-to-br from-blue-500 to-cyan-400 w-14 h-14 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg shadow-blue-500/50">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                </svg>
                            </div>
                            <h4 class="text-xl font-bold mb-3 text-white">Route Optimization</h4>
                            <p class="text-gray-300">AI-powered route planning to reduce fuel costs and delivery times</p>
                        </div>

                        <div class="bg-gradient-to-br from-blue-900/40 to-slate-900/40 backdrop-blur-md border border-white/10 rounded-2xl p-8 hover:border-cyan-500/50 transition-all duration-300 hover:shadow-xl hover:shadow-cyan-500/20 group">
                            <div class="bg-gradient-to-br from-blue-500 to-cyan-400 w-14 h-14 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg shadow-blue-500/50">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h4 class="text-xl font-bold mb-3 text-white">Automated Scheduling</h4>
                            <p class="text-gray-300">Smart scheduling system that optimizes driver assignments and delivery windows</p>
                        </div>

                        <div class="bg-gradient-to-br from-blue-900/40 to-slate-900/40 backdrop-blur-md border border-white/10 rounded-2xl p-8 hover:border-cyan-500/50 transition-all duration-300 hover:shadow-xl hover:shadow-cyan-500/20 group">
                            <div class="bg-gradient-to-br from-blue-500 to-cyan-400 w-14 h-14 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg shadow-blue-500/50">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <h4 class="text-xl font-bold mb-3 text-white">Analytics Dashboard</h4>
                            <p class="text-gray-300">Comprehensive insights and reports to make data-driven decisions</p>
                        </div>

                        <div class="bg-gradient-to-br from-blue-900/40 to-slate-900/40 backdrop-blur-md border border-white/10 rounded-2xl p-8 hover:border-cyan-500/50 transition-all duration-300 hover:shadow-xl hover:shadow-cyan-500/20 group">
                            <div class="bg-gradient-to-br from-blue-500 to-cyan-400 w-14 h-14 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg shadow-blue-500/50">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <h4 class="text-xl font-bold mb-3 text-white">Secure & Compliant</h4>
                            <p class="text-gray-300">Enterprise-grade security with full compliance to transportation regulations</p>
                        </div>

                        <div class="bg-gradient-to-br from-blue-900/40 to-slate-900/40 backdrop-blur-md border border-white/10 rounded-2xl p-8 hover:border-cyan-500/50 transition-all duration-300 hover:shadow-xl hover:shadow-cyan-500/20 group">
                            <div class="bg-gradient-to-br from-blue-500 to-cyan-400 w-14 h-14 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg shadow-blue-500/50">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h4 class="text-xl font-bold mb-3 text-white">Mobile App</h4>
                            <p class="text-gray-300">Full-featured mobile apps for drivers and managers on iOS and Android</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- CTA Section -->
            <section class="py-20 px-6">
                <div class="max-w-4xl mx-auto text-center">
                    <div class="bg-gradient-to-br from-blue-900/40 to-slate-900/40 backdrop-blur-md border border-white/10 rounded-3xl p-12">
                        <h3 class="text-4xl font-bold mb-6 bg-gradient-to-r from-blue-400 to-cyan-300 bg-clip-text text-transparent">
                            Ready to Transform Your Fleet?
                        </h3>
                        <p class="text-xl text-gray-200 mb-8">
                            Join hundreds of companies already using KTS to optimize their transport operations
                        </p>
                        <button class="px-10 py-4 bg-gradient-to-r from-blue-600 to-cyan-500 rounded-lg font-semibold text-lg hover:from-blue-700 hover:to-cyan-600 transition-all duration-300 shadow-xl shadow-blue-500/50 hover:shadow-2xl hover:shadow-blue-500/70 hover:scale-105">
                            Get Started Today
                        </button>
                    </div>
                </div>
            </section>

            <!-- Footer -->
            <footer class="py-12 px-6 border-t border-white/10 bg-black/20 backdrop-blur-sm">
                <div class="max-w-7xl mx-auto">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <div class="mb-6 md:mb-0">
                            <div class="flex items-center space-x-3 mb-2">
                                <div class="bg-gradient-to-br from-blue-500 to-cyan-400 p-2 rounded-lg shadow-lg shadow-blue-500/50">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <span class="text-xl font-bold">KTS Transport</span>
                            </div>
                            <p class="text-gray-400 text-sm">Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})</p>
                        </div>
                        <div class="text-gray-400 text-sm">
                            © 2025 KTS Transport Management. All rights reserved.
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</template>