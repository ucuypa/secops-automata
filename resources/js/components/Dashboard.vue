<template>
    <div class="p-8 max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">SecOps Automata</h1>
            
            <button 
                @click="launchScan" 
                :disabled="isScanning"
                class="bg-gray-900 hover:bg-gray-800 text-white font-semibold py-2 px-4 rounded shadow transition-all disabled:bg-gray-400 disabled:cursor-not-allowed"
            >
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
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Vulnerability / Port</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Severity</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Discovered</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="vuln in vulnerabilities" :key="vuln.id" class="hover:bg-gray-50 transition-colors">
                        <!-- Table row data remains the same -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ vuln.title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm"><span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-blue-100 text-blue-800">{{ vuln.severity }}</span></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ vuln.status }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ new Date(vuln.created_at).toLocaleString() }}</td>
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

// The new function triggered by the button click
const launchScan = async () => {
    isScanning.value = true;
    scanMessage.value = '';

    try {
        const response = await fetch('/api/scan/launch');
        const data = await response.json();
        
        // Show the success message from the backend
        scanMessage.value = data.message;
        
    } catch (error) {
        console.error('Error triggering scan:', error);
        scanMessage.value = 'Failed to connect to the scanning service.';
    } finally {
        isScanning.value = false;
        
        // Hide the message after 4 seconds
        setTimeout(() => {
            scanMessage.value = '';
        }, 4000);
    }
};

onMounted(async () => {
    try {
        // This will quietly fail until we set up the database seeder tomorrow
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