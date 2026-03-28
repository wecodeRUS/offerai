const steps = [
  {
    number: '01',
    title: 'Загружаете резюме и вакансию',
    description:
      'Агент изучает оба документа и готовит вопросы под вашу конкретную ситуацию — не шаблонные, а те, которые реально задают на таких позициях.',
    note: 'Работает с любым форматом: PDF, Word, текст',
  },
  {
    number: '02',
    title: 'Отвечаете голосом или текстом',
    description:
      'Проводите сессию так, как вам удобно. Голосовой режим помогает почувствовать формат реального интервью. Агент выслушивает, не перебивает.',
    note: 'Голосовой режим доступен с тарифа «Подготовка»',
  },
  {
    number: '03',
    title: 'Получаете разбор по пунктам',
    description:
      'Агент оценивает ответ по пяти критериям и показывает, что конкретно можно улучшить. Не «говорите увереннее», а «добавьте цифру и результат в третье предложение».',
    note: 'Плюс готовый каркас переработанного ответа',
  },
]

export default function HowItWorks() {
  return (
    <section id="kak-eto-rabotaet" className="py-16 sm:py-20 bg-white">
      <div className="max-w-6xl mx-auto px-4 sm:px-6">
        <div className="max-w-xl mb-12">
          <p className="text-xs font-medium text-accent uppercase tracking-widest mb-3">Как это работает</p>
          <h2 className="section-title mb-3">Три шага от загрузки до разбора</h2>
          <p className="section-subtitle">
            Весь процесс занимает меньше часа. Первые результаты видны после первой же сессии.
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
          {steps.map((step, idx) => (
            <div key={step.number} className="relative">
              {/* Connector line */}
              {idx < steps.length - 1 && (
                <div className="hidden md:block absolute top-5 left-full w-full h-px bg-border z-0 -translate-x-4" />
              )}

              <div className="relative z-10">
                <div className="flex items-center gap-3 mb-4">
                  <span className="text-2xl font-semibold text-accent opacity-40">{step.number}</span>
                </div>
                <h3 className="text-base font-semibold text-text-primary mb-2">{step.title}</h3>
                <p className="text-sm text-text-secondary leading-relaxed mb-3">{step.description}</p>
                <div className="inline-flex items-center gap-1.5 text-xs text-text-secondary bg-bg rounded-md px-2.5 py-1.5">
                  <svg width="12" height="12" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  {step.note}
                </div>
              </div>
            </div>
          ))}
        </div>

        <div className="mt-10 pt-8 border-t border-border flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
          <p className="text-sm text-text-secondary max-w-md">
            После каждой сессии агент запоминает, что вы уже проработали — и не повторяет одни и те же вопросы снова.
          </p>
          <a href="https://app.getofferai.ru/register" className="btn-primary flex-shrink-0">
            Начать бесплатно
          </a>
        </div>
      </div>
    </section>
  )
}
