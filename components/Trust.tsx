export default function Trust() {
  return (
    <section className="py-14 sm:py-20 bg-white">
      <div className="max-w-6xl mx-auto px-4 sm:px-6">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div className="card text-center">
            <div className="w-10 h-10 rounded-full bg-accent-light flex items-center justify-center mx-auto mb-3">
              <svg width="18" height="18" fill="none" stroke="currentColor" strokeWidth="1.5" viewBox="0 0 24 24" className="text-accent">
                <path strokeLinecap="round" strokeLinejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
              </svg>
            </div>
            <h3 className="text-sm font-semibold text-text-primary mb-1">Данные в России</h3>
            <p className="text-xs text-text-secondary">Серверы в РФ, шифрование HTTPS. Не передаём третьим лицам.</p>
          </div>

          <div className="card text-center">
            <div className="w-10 h-10 rounded-full bg-accent-light flex items-center justify-center mx-auto mb-3">
              <svg width="18" height="18" fill="none" stroke="currentColor" strokeWidth="1.5" viewBox="0 0 24 24" className="text-accent">
                <path strokeLinecap="round" strokeLinejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
              </svg>
            </div>
            <h3 className="text-sm font-semibold text-text-primary mb-1">Оценка по 5 критериям</h3>
            <p className="text-xs text-text-secondary">Конкретность, структура, релевантность, убедительность, видимость роли.</p>
          </div>

          <div className="card text-center">
            <div className="w-10 h-10 rounded-full bg-accent-light flex items-center justify-center mx-auto mb-3">
              <svg width="18" height="18" fill="none" stroke="currentColor" strokeWidth="1.5" viewBox="0 0 24 24" className="text-accent">
                <path strokeLinecap="round" strokeLinejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <h3 className="text-sm font-semibold text-text-primary mb-1">Без гарантий оффера</h3>
            <p className="text-xs text-text-secondary">Мы не обещаем оффер — но помогаем подготовиться так, чтобы шансы выросли.</p>
          </div>
        </div>
      </div>
    </section>
  )
}
