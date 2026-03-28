const steps = [
  {
    number: '01',
    title: 'Загрузите резюме и вакансию',
    description: 'Агент читает оба документа и готовит вопросы под вашу позицию.',
  },
  {
    number: '02',
    title: 'Ответьте голосом или текстом',
    description: 'Как на настоящем интервью. Можно повторять сколько угодно.',
  },
  {
    number: '03',
    title: 'Получите разбор',
    description: 'Не «говорите увереннее», а конкретно — что переформулировать и почему.',
  },
]

export default function HowItWorks() {
  return (
    <section id="kak-eto-rabotaet" className="py-14 sm:py-20 bg-bg">
      <div className="max-w-6xl mx-auto px-4 sm:px-6">
        <div className="text-center mb-12">
          <p className="text-xs font-medium text-accent uppercase tracking-widest mb-3">Как это работает</p>
          <h2 className="section-title">Три шага — и вы готовы</h2>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          {steps.map((step) => (
            <div key={step.number} className="text-center">
              <span className="inline-block text-4xl font-bold text-accent/20 mb-3">{step.number}</span>
              <h3 className="text-base font-semibold text-text-primary mb-2">{step.title}</h3>
              <p className="text-sm text-text-secondary leading-relaxed">{step.description}</p>
            </div>
          ))}
        </div>

        <div className="text-center mt-10">
          <a href="https://app.getofferai.ru/register" className="btn-primary">
            Начать бесплатно
          </a>
        </div>
      </div>
    </section>
  )
}
