export default function Stats() {
  const stats = [
    {
      number: '5 000+',
      title: 'Звонков с HR-специалистами',
      description: 'Агент прослушал и проанализировал реальные интервью, чтобы понять, какие ответы работают, а какие — нет.',
    },
    {
      number: '46 000+',
      title: 'Резюме в базе',
      description: 'Агент опирается на большую базу реальных резюме, чтобы находить слабые и сильные места в вашем тексте.',
    },
    {
      number: '91%',
      suffix: '*',
      title: 'Проходят ATS после разбора',
      description: 'Столько кандидатов стали проходить автоматическую фильтрацию работодателей после того, как агент разобрал их резюме.',
    },
  ]

  return (
    <section className="bg-accent-light border-y border-border py-12">
      <div className="max-w-6xl mx-auto px-4 sm:px-6">
        <div className="grid grid-cols-1 sm:grid-cols-3 gap-8 sm:gap-6">
          {stats.map((stat) => (
            <div key={stat.title} className="flex flex-col gap-2">
              <div className="flex items-baseline gap-0.5">
                <span className="text-3xl font-semibold text-accent">{stat.number}</span>
                {stat.suffix && (
                  <span className="text-sm text-accent font-medium align-super">{stat.suffix}</span>
                )}
              </div>
              <p className="text-sm font-medium text-text-primary">{stat.title}</p>
              <p className="text-sm text-text-secondary leading-relaxed">{stat.description}</p>
            </div>
          ))}
        </div>

        {/* Footnote */}
        <p className="mt-8 text-xs text-text-secondary border-t border-border pt-4">
          * ATS (Applicant Tracking System) — система автоматической фильтрации резюме, которую используют крупные работодатели для первичного отбора кандидатов.
        </p>
      </div>
    </section>
  )
}
