const plans = [
  {
    name: 'Старт',
    price: '490',
    description: 'Если вы хотите попробовать и понять, как это работает.',
    features: [
      '8 сессий',
      'Разбор резюме под вакансию',
      'Оценка ответов по 5 критериям',
      'Текстовый режим',
      'Рекомендации после каждой сессии',
    ],
    cta: 'Начать за 490 ₽',
    highlighted: false,
  },
  {
    name: 'Подготовка',
    price: '990',
    description: 'Для тех, кто хочет подготовиться основательно — голосом и вживую.',
    features: [
      '25 сессий',
      'Всё из тарифа Старт',
      'Голосовой режим',
      'Пробное интервью целиком',
      'Прогресс по сессиям',
    ],
    cta: 'Начать за 990 ₽',
    highlighted: true,
    badge: 'Популярный',
  },
  {
    name: 'Оффер',
    price: '1 990',
    description: 'Когда важно не просто пройти — а получить те условия, на которые вы рассчитываете.',
    features: [
      '40 сессий',
      'Всё из тарифа Подготовка',
      'Разбор конкретного оффера',
      'Практика переговоров по зарплате',
      'Подготовка к финальному раунду',
    ],
    cta: 'Начать за 1 990 ₽',
    highlighted: false,
  },
]

export default function Pricing() {
  return (
    <section id="tarify" className="py-16 sm:py-20 bg-bg">
      <div className="max-w-6xl mx-auto px-4 sm:px-6">
        <div className="max-w-xl mb-12">
          <p className="text-xs font-medium text-accent uppercase tracking-widest mb-3">Тарифы</p>
          <h2 className="section-title mb-3">Платите один раз, используете пока не закончатся сессии</h2>
          <p className="section-subtitle">
            Никаких подписок и автосписаний. Купили пакет — используете в своём темпе.
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
          {plans.map((plan) => (
            <div
              key={plan.name}
              className={`rounded-xl border p-6 flex flex-col ${
                plan.highlighted
                  ? 'bg-white border-accent shadow-md'
                  : 'bg-white border-border'
              }`}
            >
              {/* Header */}
              <div className="flex items-start justify-between mb-1">
                <h3 className="text-base font-semibold text-text-primary">{plan.name}</h3>
                {plan.badge && (
                  <span className="text-xs font-medium bg-accent-light text-accent px-2 py-0.5 rounded-full">
                    {plan.badge}
                  </span>
                )}
              </div>

              {/* Price */}
              <div className="flex items-baseline gap-1 mb-2">
                <span className="text-3xl font-semibold text-text-primary">{plan.price}</span>
                <span className="text-text-secondary text-sm">₽</span>
              </div>

              <p className="text-sm text-text-secondary mb-5 leading-relaxed">{plan.description}</p>

              {/* Features */}
              <ul className="space-y-2 mb-6 flex-1">
                {plan.features.map((feature) => (
                  <li key={feature} className="flex items-start gap-2 text-sm text-text-primary">
                    <svg
                      width="16"
                      height="16"
                      fill="none"
                      stroke="currentColor"
                      strokeWidth="2"
                      viewBox="0 0 24 24"
                      className="text-accent flex-shrink-0 mt-0.5"
                    >
                      <path strokeLinecap="round" strokeLinejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    {feature}
                  </li>
                ))}
              </ul>

              {/* CTA */}
              <a
                href="https://app.getofferai.ru/register"
                className={`w-full text-center py-2.5 px-4 rounded-lg text-sm font-medium transition-colors ${
                  plan.highlighted
                    ? 'bg-accent text-white hover:bg-opacity-90'
                    : 'bg-bg text-text-primary border border-border hover:border-accent hover:text-accent'
                }`}
              >
                {plan.cta}
              </a>
            </div>
          ))}
        </div>

        <p className="mt-6 text-center text-sm text-text-secondary">
          Первые 3 сессии бесплатно — карта не нужна, регистрация за 30 секунд
        </p>
      </div>
    </section>
  )
}
