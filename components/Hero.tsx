export default function Hero() {
  return (
    <section className="bg-bg pt-16 pb-12 sm:pt-20 sm:pb-16">
      <div className="max-w-6xl mx-auto px-4 sm:px-6">
        <div className="max-w-2xl">
          {/* Label */}
          <div className="inline-flex items-center gap-2 bg-accent-light text-accent text-xs font-medium px-3 py-1.5 rounded-full mb-6">
            <span className="w-1.5 h-1.5 bg-accent rounded-full"></span>
            Первые 3 сессии бесплатно
          </div>

          {/* Heading */}
          <h1 className="text-3xl sm:text-4xl lg:text-5xl font-semibold text-text-primary leading-tight tracking-tight mb-5">
            Готовьтесь к собеседованию на своём резюме — не по абстрактным советам
          </h1>

          {/* Subheading */}
          <p className="text-lg text-text-secondary leading-relaxed mb-8 max-w-xl">
            Загрузите резюме и описание вакансии. Агент задаст реальные вопросы под вашу ситуацию, выслушает ответы и скажет, что именно не так — без воды.
          </p>

          {/* CTAs */}
          <div className="flex flex-col sm:flex-row items-start sm:items-center gap-3 mb-5">
            <a
              href="https://app.getofferai.ru/register"
              className="btn-primary px-6 py-3 text-base"
            >
              Попробовать бесплатно
            </a>
            <a
              href="#demo"
              className="inline-flex items-center gap-1.5 text-sm font-medium text-text-secondary hover:text-text-primary transition-colors"
            >
              <svg width="16" height="16" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" />
                <path strokeLinecap="round" strokeLinejoin="round" d="M10 8l6 4-6 4V8z" />
              </svg>
              Смотреть пример разбора
            </a>
          </div>

          {/* Trust note */}
          <p className="text-xs text-text-secondary">
            Карта не нужна · Без подписки · Работает для любой специальности
          </p>
        </div>

        {/* Visual mockup */}
        <div className="mt-12 max-w-2xl">
          <div className="bg-white rounded-2xl border border-border shadow-sm overflow-hidden">
            {/* Mockup header */}
            <div className="flex items-center gap-1.5 px-4 py-3 border-b border-border bg-bg">
              <div className="w-2.5 h-2.5 rounded-full bg-red-300"></div>
              <div className="w-2.5 h-2.5 rounded-full bg-yellow-300"></div>
              <div className="w-2.5 h-2.5 rounded-full bg-green-300"></div>
              <span className="ml-3 text-xs text-text-secondary">OfferAI — разбор ответа</span>
            </div>

            {/* Mockup content */}
            <div className="p-5 space-y-4">
              {/* Context */}
              <div className="flex items-start gap-3">
                <div className="w-6 h-6 rounded-full bg-accent-light flex-shrink-0 flex items-center justify-center mt-0.5">
                  <span className="text-accent text-xs font-semibold">HR</span>
                </div>
                <div>
                  <p className="text-sm text-text-secondary text-xs mb-1">Backend-разработчик · Java · Fintech</p>
                  <p className="text-sm text-text-primary font-medium">Расскажите о самом сложном техническом проекте, в котором вы участвовали.</p>
                </div>
              </div>

              {/* Candidate answer */}
              <div className="flex items-start gap-3">
                <div className="w-6 h-6 rounded-full bg-border flex-shrink-0 flex items-center justify-center mt-0.5">
                  <span className="text-text-secondary text-xs font-semibold">Я</span>
                </div>
                <div className="bg-bg rounded-lg px-3 py-2.5 text-sm text-text-secondary">
                  Мы делали большой сервис для банка. Было много сложностей с производительностью и интеграциями. В итоге всё получилось, я многому научился.
                </div>
              </div>

              {/* Score */}
              <div className="bg-bg rounded-xl p-4 space-y-2.5">
                <p className="text-xs font-medium text-text-secondary uppercase tracking-wide">Разбор по критериям</p>
                {[
                  { label: 'Конкретность', score: 2, max: 5 },
                  { label: 'Структура', score: 1, max: 5 },
                  { label: 'Попадание в вакансию', score: 3, max: 5 },
                ].map((item) => (
                  <div key={item.label} className="flex items-center gap-3">
                    <span className="text-xs text-text-secondary w-36">{item.label}</span>
                    <div className="flex gap-1">
                      {Array.from({ length: item.max }).map((_, i) => (
                        <div
                          key={i}
                          className={`w-4 h-1.5 rounded-full ${i < item.score ? 'bg-highlight' : 'bg-border'}`}
                        />
                      ))}
                    </div>
                    <span className="text-xs text-text-secondary">{item.score}/{item.max}</span>
                  </div>
                ))}
              </div>

              {/* Suggestion */}
              <div className="border-l-2 border-accent pl-3">
                <p className="text-xs font-medium text-accent mb-1">Что можно улучшить</p>
                <p className="text-xs text-text-secondary">Добавьте конкретику: название системы, объём нагрузки, ваша роль и что именно вы сделали для решения проблемы с производительностью.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  )
}
