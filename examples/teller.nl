hoi

// Tellen met een lus
stel i = 1;
zolang (i <= 10) {
    als (i == 5) {
        zeg "Vijf! Doorgaan...";
        i += 1;
        verder;
    }
    als (i == 8) {
        zeg "Stop bij acht.";
        stop;
    }
    zeg i;
    i += 1;
}

// Fibonacci reeks
zeg "--- Fibonacci ---";
stel a = 0, b = 1, stap = 0;
zolang (stap < 10) {
    zeg a;
    stel c = a + b;
    a = b;
    b = c;
    stap += 1;
}

doei
