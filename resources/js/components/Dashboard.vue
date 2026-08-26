<template>
    <div class="p-8 max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">SecOps Automata</h1>

            <button @click="launchScan" :disabled="isScanning"
                class="bg-gray-900 hover:bg-gray-800 text-white font-semibold py-2 px-4 rounded shadow transition-all disabled:bg-gray-400 disabled:cursor-not-allowed">
                {{ isScanning ? 'Initiating Scan...' : 'Launch Scan' }}
            </button>
        </div>

        <!-- temporary alert to show the backend response -->
        <div v-if="scanMessage" class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ scanMessage }}
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <!-- Table headers remain the same -->
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Vulnerability / Port</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Severity</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Discovered</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="(vuln, index) in vulnerabilities" :key="index"
                        class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ vuln.title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm"><span
                                class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-blue-100 text-blue-800">{{
                                vuln.severity }}</span></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ vuln.status }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ vuln.discovered_at }}</td>
                    </tr>
                    <tr v-if="vulnerabilities.length === 0">
                        <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">
                            No vulnerabilities found. The infrastructure is currently secure.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const vulnerabilities = ref([]);
const isScanning = ref(false);
const scanMessage = ref('');

const launchScan = async () => {

    alert('Button was clicked!');
    isScanning.value = true;
    scanMessage.value = '';

    try {
        const response = await fetch('/api/scan/launch');
        const data = await response.json();
        
        // DEBUG: Look at this in your browser console (F12 -> Console)
        console.log("Full API Response Data:", data);
        
        scanMessage.value = data.message;
        
        if (data.findings) {
            vulnerabilities.value = data.findings;
        }
        
    } catch (error) {
        console.error('Error triggering scan:', error);
    } finally {
        isScanning.value = false;
    }
};

onMounted(async () => {
    try {
        const response = await fetch('/api/vulnerabilities');
        if (response.ok) {
            const data = await response.json();
            vulnerabilities.value = data;
        }
    } catch (error) {
        console.error('Error fetching security data:', error);
    }
});
</script>