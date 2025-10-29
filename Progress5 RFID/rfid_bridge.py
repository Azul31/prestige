import serial
import time
import requests
from urllib.parse import quote

# Configure these settings:
SERIAL_PORT = 'COM5'  # Change this to match your Arduino's port (COM3, COM4, etc)
BAUD_RATE = 9600
CARD_FILE = 'last_card.txt'
SAVE_URL = 'http://localhost/prestige_rfid/save_rfid.php'

def setup_serial():
    """Setup and return serial connection to Arduino"""
    try:
        ser = serial.Serial(SERIAL_PORT, BAUD_RATE, timeout=1)
        print(f"Connected to Arduino on {SERIAL_PORT}")
        time.sleep(2)  # Wait for Arduino to reset
        return ser
    except serial.SerialException as e:
        print(f"Error connecting to Arduino: {e}")
        return None

def save_card_uid(uid):
    """Save card UID to file and notify web server"""
    try:
        # Save to file
        with open(CARD_FILE, 'w') as f:
            f.write(uid)
        print(f"Saved UID to {CARD_FILE}: {uid}")
        
        # Notify web server
        response = requests.get(f"{SAVE_URL}?uid={quote(uid)}")
        if response.ok:
            print("Successfully notified web server")
            print("Response:", response.json())
        else:
            print(f"Error notifying server: {response.status_code}")
            print("Response:", response.text)
    except Exception as e:
        print(f"Error saving card data: {e}")

def main():
    print("Starting RFID Reader Bridge")
    print("Make sure Arduino is connected and running")
    
    ser = setup_serial()
    if not ser:
        return
    
    print("Waiting for RFID cards...")
    
    while True:
        try:
            if ser.in_waiting:
                line = ser.readline().decode('utf-8').strip()
                if line:
                    print(f"Received from Arduino: {line}")
                    save_card_uid(line)
        except serial.SerialException as e:
            print(f"Serial error: {e}")
            time.sleep(1)
            ser = setup_serial()  # Try to reconnect
        except KeyboardInterrupt:
            print("\nExiting...")
            break
        except Exception as e:
            print(f"Error: {e}")
            time.sleep(1)

if __name__ == "__main__":
    main()