import subprocess
import json
import re
import sys
from datetime import datetime

TARGET = "127.0.0.1"

def run_nmap():
    # print(f"Starting automated Nmap scan on {TARGET}...")
    try:
        command = ["nmap", "-p", "21,22,80,443,3306,5432,6379,8000", TARGET]
        
        result = subprocess.run(command, capture_output=True, text=True, check=True)
        return result.stdout
    except subprocess.CalledProcessError as e:
        print(f"[!] Nmap scan failed: {e}")
        sys.exit(1)
    except FileNotFoundError:
        print("[!] Error: 'nmap' is not installed or not found in system PATH.")
        sys.exit(1)

def parse_nmap_output(output):
    vulnerabilities = []
    lines = output.split('\n')
    
    for line in lines:
        if "open" in line and "/tcp" in line:
            parts = line.split()
            port_proto = parts[0]
            service = parts[2] if len(parts) > 2 else "unknown"
            
            vuln_item = {
                "title": f"Open Port Detected: {port_proto} ({service})",
                "severity": "Medium" if service in ["http", "https"] else "High",
                "status": "Active",
                "discovered_at": datetime.now().strftime("%Y-%m-%d %H:%M:%S")
            }
            vulnerabilities.append(vuln_item)
            
    return vulnerabilities

if __name__ == "__main__":
    raw_output = run_nmap()
    parsed_data = parse_nmap_output(raw_output)
    
    print(json.dumps(parsed_data, indent=4))