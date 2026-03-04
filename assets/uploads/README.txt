This folder stores uploaded files.

/tickets   — Payment proof photos and generated QR codes
/candidates — Candidate profile photos

These folders must be WRITABLE by the web server.

Windows XAMPP: Right-click folder > Properties > Security > Edit > Allow Write for Users
Linux/Mac:     chmod -R 755 uploads/

Files are protected from direct browsing via .htaccess
