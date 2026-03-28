const trustItems = [
  {
    icon: (
      <svg width="20" height="20" fill="none" stroke="currentColor" strokeWidth="1.5" viewBox="0 0 24 24">
        <path strokeLinecap="round" strokeLinejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
    ),
    title: 'Что агент не гарантирует',
    description:
      'Мы не можем обещать, что вы получите оффер — это зависит от слишком многих факторов. Агент помогает подготовиться качественнее, но решение принимает компания.',
  },
  {
    icon: (
      <svg width="20" height="20" fill="none" stroke="currentColor" strokeWidth="1.5" viewBox="0 0 24 24">
        <path strokeLinecap="round" strokeLinejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
      </svg>
    ),
    title: 'Как работает оценка',
    description:
      'Агент анализирует ответы по пяти параметрам: конкретность, структура, попадание в требования вакансии, убедительность и то, насколько видна ваша роль. Каждый критерий — по шкале от 1 до 5.',
  },
  {
    icon: (
      <svg width="20" height="20" fill="none" stroke="currentColor" strokeWidth="1.5" viewBox="0 0 24 24">
        <path strokeLinecap="round" strokeLinejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
      </svg>
    ),
    title: 'Ваши данные',
    description:
      'Серверы находятся в России. Соединение защищено HTTPS. Мы не передаём ваши данные третьим лицам и не используем их для рекламы. Резюме и ответы хранятся только для того, чтобы агент помнил ваш прогресс.',
  },
  {
    icon: (
      <svg width="20" height="20" fill="none" stroke="currentColor" strokeWidth="1.5" viewBox="0 0 24 24">
        <path strokeLinecap="round" strokeLinejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
      </svg>
    ),
    title: 'Откуда берутся вопросы',
    description:
      'Агент формирует вопросы на основе вашего резюме, требований из вакансии и базы реальных вопросов, которые задают на интервью в вашей области. Это не случайный набор.',
  },
]

export default function Trust() {
  return (
    <section className="py-16 sm:py-20 bg-white">
      <div className="max-w-6xl mx-auto px-4 sm:px-6">
        <div className="max-w-xl mb-12">
          <p className="text-xs font-medium text-accent uppercase tracking-widest mb-3">Честно о продукте</p>
          <h2 className="section-title mb-3">Несколько вещей, которые стоит знать</h2>
          <p className="section-subtitle">
            Мы стараемся не приукрашивать. Вот как работает сервис на самом деле.
          </p>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
          {trustItems.map((item) => (
            <div key={item.title} className="flex gap-4">
              <div className="w-9 h-9 rounded-lg bg-bg border border-border text-text-secondary flex items-center justify-center flex-shrink-0 mt-0.5">
                {item.icon}
              </div>
              <div>
                <h3 className="text-sm font-semibold text-text-primary mb-1.5">{item.title}</h3>
                <p className="text-sm text-text-secondary leading-relaxed">{item.description}</p>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  )
}
