hoi

stel score = 75;

als (score >= 90) {
    zeg "Uitstekend!";
} anders als (score >= 70) {
    zeg "Goed gedaan!";
} anders als (score >= 50) {
    zeg "Voldoende.";
} anders {
    zeg "Onvoldoende.";
}

// Geneste blokken en scoping
stel x = 5;
{
    stel x = 99;
    zeg "Binnen blok: x =", x;
}
zeg "Buiten blok: x =", x;

doei
