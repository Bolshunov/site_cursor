#!/usr/bin/env python3
"""
Локальный просмотр зеркала: корень всегда эта папка (не важно, откуда запустили).

  python serve.py

Открой в браузере:
  http://127.0.0.1:8765/about/index.html
  http://127.0.0.1:8765/about/   (index.html подхватится автоматически)
"""
from __future__ import annotations

import functools
import http.server
import socketserver
from pathlib import Path

ROOT = Path(__file__).resolve().parent
PORT = 8765

Handler = functools.partial(
    http.server.SimpleHTTPRequestHandler,
    directory=str(ROOT),
)


def main() -> None:
    with socketserver.TCPServer(("", PORT), Handler) as httpd:
        print(f"Корень зеркала: {ROOT}")
        print(f"http://127.0.0.1:{PORT}/")
        print(f"http://127.0.0.1:{PORT}/about/index.html")
        httpd.serve_forever()


if __name__ == "__main__":
    main()
