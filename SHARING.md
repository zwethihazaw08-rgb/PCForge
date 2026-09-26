# Share PCForge on your local network

Keep Apache and MySQL running in XAMPP, then run this from the PCForge folder:

```powershell
python share_site.py --copy
```

The Python script uses only the standard library. It detects the IPv4 address
selected by your default network route, prints the website URL, and copies it
to your Windows clipboard. Omit `--copy` to only print it.
To choose a specific network or Apache port:

```powershell
python share_site.py --ipv4 192.168.10.105 --port 80 --copy
```

Open the generated link on another device connected to the same Wi-Fi or LAN.
The link serves this PC's current files and database; refresh to see saved changes.
It does not automatically reload other devices. Keep this PC awake while sharing.
Run the script again if your network address changes.

If another device cannot connect, check that Windows Firewall allows Apache on
your private network and that your router does not isolate guest Wi-Fi clients.
This is a local-network link, not a public internet hosting link.
