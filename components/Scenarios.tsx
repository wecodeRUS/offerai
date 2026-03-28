const scenarios = [
  {
    title: 'Интервью через пару дней',
    description: 'Быстро прогнать ключевые вопросы и не теряться на встрече.',
  },
  {
    title: 'Меняете сферу',
    description: 'Объяснить переход так, чтобы это звучало осознанно, а не случайно.',
  },
  {
    title: '«Расскажите о себе»',
    description: 'Собрать короткий, чёткий рассказ — без воды и без пересказа резюме.',
  },
  {
    title: 'Хотите оффер получше',
    description: 'Потренировать переговоры по зарплате и разобрать условия.',
  },
]

export default function Scenarios() {
  return (
    <section id="stsenarii" className="py-14 sm:py-20 bg-white">
      <div className="max-w-6xl mx-auto px-4 sm:px-6">
        <div className="text-center mb-10">
          <p className="text-xs font-medium text-accent uppercase tracking-widest mb-3">Для кого</p>
          <h2 className="section-title">Когда это пригодится</h2>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          {scenarios.map((s) => (
            <div key={s.title} className="card hover:border-accent transition-colors duration-200 text-center">
              <h3 className="text-sm font-semibold text-text-primary mb-2">{s.title}</h3>
              <p className="text-sm text-text-secondary leading-relaxed">{s.description}</p>
            </div>
          ))}
        </div>
      </div>
    </section>
  )
}
