const plans = [
  {
    name: 'Старт',
    price: '490',
    note: 'Попробовать',
    features: ['8 сессий', 'Разбор резюме под вакансию', 'Оценка по 5 критериям', 'Текстовый режим'],
    highlighted: false,
  },
  {
    name: 'Подготовка',
    price: '990',
    note: 'Подготовиться основательно',
    badge: 'Популярный',
    features: ['25 сессий', 'Всё из Старта', 'Голосовой режим', 'Пробное интервью'],
    highlighted: true,
  },
  {
    name: 'Оффер',
    price: '1 990',
    note: 'Получить лучшие условия',
    features: ['40 сессий', 'Всё из Подготовки', 'Переговоры по зарплате', 'Разбор оффера'],
    highlighted: false,
  },
]

export default function Pricing() {
  return (
    <section id="tarify" className="py-14 sm:py-20 bg-bg">
      <div className="max-w-6xl mx-auto px-4 sm:px-6">
        <div className="text-center mb-10">
          <p className="text-xs font-medium text-accent uppercase tracking-widest mb-3">Тарифы</p>
          <h2 className="section-title mb-3">Одна оплата — без подписок</h2>
          <p className="text-sm text-text-secondary">Купили пакет — используете в своём темпе. Ничего не спишется автоматически.</p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-5 max-w-4xl mx-auto">
          {plans.map((plan) => (
            <div
              key={plan.name}
              className={`rounded-xl border p-6 flex flex-col ${
                plan.highlighted ? 'bg-white border-accent shadow-lg relative' : 'bg-white border-border'
              }`}
            >
              {plan.badge && (
                <span className="absolute -top-3 left-1/2 -translate-x-1/2 text-[11px] font-medium bg-accent text-white px-3 py-1 rounded-full">
                  {plan.badge}
                </span>
              )}

              <h3 className="text-sm font-semibold text-text-primary mb-1">{plan.name}</h3>
              <div className="flex items-baseline gap-1 mb-1">
                <span className="text-3xl font-bold text-text-primary">{plan.price}</span>
                <span className="text-text-secondary text-sm">₽</span>
              </div>
              <p className="text-xs text-text-secondary mb-4">{plan.note}</p>

              <ul className="space-y-2 mb-5 flex-1">
                {plan.features.map((f) => (
                  <li key={f} className="flex items-center gap-2 text-sm text-text-primary">
                    <svg width="14" height="14" fill="none" stroke="currentColor" strokeWidth="2.5" viewBox="0 0 24 24" className="text-accent flex-shrink-0">
                      <path strokeLinecap="round" strokeLinejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    {f}
                  </li>
                ))}
              </ul>

              <a
                href="https://app.getofferai.ru/register"
                className={`w-full text-center py-2.5 rounded-lg text-sm font-medium transition-colors ${
                  plan.highlighted
                    ? 'bg-accent text-white hover:bg-accent-dark'
                    : 'bg-bg text-text-primary border border-border hover:border-accent hover:text-accent'
                }`}
              >
                Начать за {plan.price} ₽
              </a>
            </div>
          ))}
        </div>

        <p className="mt-6 text-center text-xs text-text-secondary">
          3 сессии бесплатно — карта не нужна
        </p>
      </div>
    </section>
  )
}
