'use client'

import { useState } from 'react'

export default function Header() {
  const [menuOpen, setMenuOpen] = useState(false)

  const navLinks = [
    { href: '#kak-eto-rabotaet', label: 'Как это работает' },
    { href: '#stsenarii', label: 'Сценарии' },
    { href: '#sravnenie', label: 'Сравнение' },
    { href: '#tarify', label: 'Тарифы' },
    { href: '#faq', label: 'FAQ' },
  ]

  return (
    <header className="sticky top-0 z-50 bg-white border-b border-border">
      <div className="max-w-6xl mx-auto px-4 sm:px-6">
        <div className="flex items-center justify-between h-14">
          {/* Logo */}
          <a href="/" className="flex items-center gap-1 font-semibold text-lg text-text-primary">
            Offer<span className="text-accent">AI</span>
          </a>

          {/* Desktop nav */}
          <nav className="hidden md:flex items-center gap-6">
            {navLinks.map((link) => (
              <a
                key={link.href}
                href={link.href}
                className="text-sm text-text-secondary hover:text-text-primary transition-colors"
              >
                {link.label}
              </a>
            ))}
          </nav>

          {/* Desktop buttons */}
          <div className="hidden md:flex items-center gap-2">
            <a
              href="https://app.getofferai.ru/login"
              className="btn-ghost text-sm"
            >
              Войти
            </a>
            <a
              href="https://app.getofferai.ru/register"
              className="btn-primary text-sm"
            >
              Начать бесплатно
            </a>
          </div>

          {/* Mobile menu button */}
          <button
            className="md:hidden p-2 text-text-secondary hover:text-text-primary"
            onClick={() => setMenuOpen(!menuOpen)}
            aria-label="Меню"
          >
            {menuOpen ? (
              <svg width="20" height="20" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            ) : (
              <svg width="20" height="20" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            )}
          </button>
        </div>

        {/* Mobile menu */}
        {menuOpen && (
          <div className="md:hidden border-t border-border py-3 space-y-1">
            {navLinks.map((link) => (
              <a
                key={link.href}
                href={link.href}
                className="block py-2 px-1 text-sm text-text-secondary hover:text-text-primary"
                onClick={() => setMenuOpen(false)}
              >
                {link.label}
              </a>
            ))}
            <div className="pt-2 flex flex-col gap-2">
              <a href="https://app.getofferai.ru/login" className="btn-ghost text-sm w-full text-center">
                Войти
              </a>
              <a href="https://app.getofferai.ru/register" className="btn-primary text-sm w-full text-center">
                Начать бесплатно
              </a>
            </div>
          </div>
        )}
      </div>
    </header>
  )
}
