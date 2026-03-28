export default function Footer() {
  return (
    <footer className="bg-white border-t border-border py-10">
      <div className="max-w-6xl mx-auto px-4 sm:px-6">
        <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
          {/* Logo + copyright */}
          <div>
            <a href="/" className="text-base font-semibold text-text-primary">
              Offer<span className="text-accent">AI</span>
            </a>
            <p className="text-xs text-text-secondary mt-1">
              © {new Date().getFullYear()} OfferAI. Все права защищены.
            </p>
          </div>

          {/* Links */}
          <div className="flex flex-wrap gap-x-6 gap-y-2">
            <a
              href="mailto:support@getofferai.ru"
              className="text-xs text-text-secondary hover:text-text-primary transition-colors"
            >
              support@getofferai.ru
            </a>
            <a
              href="/oferta"
              className="text-xs text-text-secondary hover:text-text-primary transition-colors"
            >
              Договор оферты
            </a>
            <a
              href="/privacy"
              className="text-xs text-text-secondary hover:text-text-primary transition-colors"
            >
              Политика конфиденциальности
            </a>
            <a
              href="/consent"
              className="text-xs text-text-secondary hover:text-text-primary transition-colors"
            >
              Согласие на обработку данных
            </a>
          </div>
        </div>
      </div>
    </footer>
  )
}
