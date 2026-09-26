"""Print the LAN link for the existing XAMPP PCForge website."""

import argparse
import ipaddress
import socket
import subprocess
import sys


def get_share_url(ipv4=None, port=80):
    """Use an explicit IPv4 or the address selected by the default route."""
    if not 1 <= port <= 65535:
        raise ValueError("Port must be between 1 and 65535.")
    if ipv4 is None:
        # UDP connect selects a local route without sending any data.
        with socket.socket(socket.AF_INET, socket.SOCK_DGRAM) as connection:
            connection.connect(("192.0.2.1", 80))
            ipv4 = connection.getsockname()[0]

    address = ipaddress.IPv4Address(ipv4)
    if (address.is_loopback or address.is_link_local or address.is_unspecified
            or address.is_multicast or address.is_reserved):
        raise ValueError("Use your Wi-Fi or LAN IPv4 address.")
    suffix = "" if port == 80 else f":{port}"
    return f"http://{address}{suffix}/PCForge/"


def main():
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--ipv4", help="Override automatic network detection")
    parser.add_argument("--port", type=int, default=80, help="Apache port (default: 80)")
    parser.add_argument("--copy", action="store_true", help="Copy link on Windows")
    args = parser.parse_args()
    try:
        link = get_share_url(args.ipv4, args.port)
    except (OSError, ValueError) as error:
        parser.exit(1, f"Could not generate link: {error}\nTry --ipv4 YOUR_LAN_ADDRESS.\n")

    print(link, flush=True)
    if args.copy:
        if sys.platform != "win32":
            parser.exit(1, "Automatic clipboard copy is supported on Windows only.\n")
        try:
            subprocess.run(["clip.exe"], input=link.encode("utf-16le"), check=True)
        except (OSError, subprocess.CalledProcessError) as error:
            parser.exit(1, f"Link generated, but clipboard copy failed: {error}\n")


if __name__ == "__main__":
    main()
