#!/bin/bash

# Zistíme cestu k aktuálnemu skriptu, aby sme mohli správne nájsť virtuálne prostredie a PID súbor
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
VENV_DIR="$SCRIPT_DIR/venv"  # upravte, ak je virtuálne prostredie inde
PID_FILE="$SCRIPT_DIR/uvicorn.pid"
PORT=5050

# Aktivujeme virtuálne prostredie
echo "Aktivujem virtuálne prostredie z: $VENV_DIR"
source "$VENV_DIR/bin/activate"

if [ "$1" = "start" ]; then
    echo "Spúšťam API na porte $PORT na pozadí..."
    nohup uvicorn main:app --host 127.0.0.1 --port $PORT --reload > /dev/null 2>&1 &
    echo $! > "$PID_FILE"
    echo "Server spustený (PID: $(cat "$PID_FILE"))"
elif [ "$1" = "stop" ]; then
    if [ -f "$PID_FILE" ]; then
        PID=$(cat "$PID_FILE")
        echo "Zastavujem server (PID: $PID)..."
        kill $PID
        rm "$PID_FILE"
        echo "Server zastavený."
    else
        echo "PID súbor neexistuje. Server pravdepodobne nie je spustený."
    fi
else
    echo "Použitie: $0 {start|stop}"
fi
