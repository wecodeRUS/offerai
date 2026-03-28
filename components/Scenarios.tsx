const scenarios = [
  {
    icon: (
      <svg width="20" height="20" fill="none" stroke="currentColor" strokeWidth="1.5" viewBox="0 0 24 24">
        <path strokeLinecap="round" strokeLinejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
    ),
    title: 'Интервью через несколько дней',
    description:
      'Нет времени на долгую подготовку. Загружаете вакансию — агент сразу видит, какие вопросы там типичны, и прорабатываете именно их.',
    steps: ['Загрузить резюме и вакансию', 'Пройти 2–3 сессии по ключевым вопросам', 'Отполировать ответы на «расскажите о себе»'],
  },
  {
    icon: (
      <svg width="20" height="20" fill="none" stroke="currentColor" strokeWidth="1.5" viewBox="0 0 24 24">
        <path strokeLinecap="round" strokeLinejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
      </svg>
    ),
    title: 'Меняете сферу или уровень',
    description:
      'Сложно объяснить, почему вы переходите из одной области в другую — или почему вы готовы к позиции выше. Агент поможет выстроить логику перехода.',
    steps: ['Объяснить переход без оправданий', 'Показать, что опыт переносится', 'Убрать неловкие паузы в ответе'],
  },
  {
    icon: (
      <svg width="20" height="20" fill="none" stroke="currentColor" strokeWidth="1.5" viewBox="0 0 24 24">
        <path strokeLinecap="round" strokeLinejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
      </svg>
    ),
    title: 'Теряетесь на вопросах про себя',
    description:
      '«Расскажите о себе», «каковы ваши сильные стороны» — эти вопросы звучат просто, но именно на них люди часто отвечают размыто или слишком длинно.',
    steps: ['Собрать чёткий рассказ о себе за 2 минуты', 'Ответить на вопросы про слабые стороны без лишних самокопаний', 'Говорить о мотивации конкретно'],
  },
  {
    icon: (
      <svg width="20" height="20" fill="none" stroke="currentColor" strokeWidth="1.5" viewBox="0 0 24 24">
        <path strokeLinecap="round" strokeLinejoin="round" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z" />
      </svg>
    ),
    title: 'Хотите конкретный оффер',
    description:
      'Готовитесь к финальному раунду или хотите грамотно обсудить условия. Агент разбирает, как говорить о зарплате и что делать, если оффер не устраивает.',
    steps: ['Пройти пробное финальное интервью', 'Отработать переговоры по зарплате', 'Разобрать конкретный оффер с агентом'],
  },
]

export default function Scenarios() {
  return (
    <section id="stsenarii" className="py-16 sm:py-20 bg-bg">
      <div className="max-w-6xl mx-auto px-4 sm:px-6">
        <div className="max-w-xl mb-12">
          <p className="text-xs font-medium text-accent uppercase tracking-widest mb-3">Сценарии</p>
          <h2 className="section-title mb-3">Для каких ситуаций это подходит</h2>
          <p className="section-subtitle">
            Неважно, ищете ли вы первую работу или готовитесь к переходу в другую компанию — подготовка под конкретную вакансию работает лучше, чем общие советы.
          </p>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
          {scenarios.map((scenario) => (
            <div key={scenario.title} className="card hover:border-accent hover:shadow-sm transition-all duration-200">
              <div className="flex items-start gap-3 mb-4">
                <div className="w-9 h-9 rounded-lg bg-accent-light text-accent flex items-center justify-center flex-shrink-0">
                  {scenario.icon}
                </div>
                <h3 className="text-base font-semibold text-text-primary pt-1">{scenario.title}</h3>
              </div>
              <p className="text-sm text-text-secondary leading-relaxed mb-4">{scenario.description}</p>
              <ul className="space-y-1.5">
                {scenario.steps.map((step) => (
                  <li key={step} className="flex items-start gap-2 text-xs text-text-secondary">
                    <span className="w-1 h-1 rounded-full bg-accent mt-1.5 flex-shrink-0" />
                    {step}
                  </li>
                ))}
              </ul>
            </div>
          ))}
        </div>
      </div>
    </section>
  )
}
