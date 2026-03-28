const rows = [
  { feature: 'Учитывает ваше резюме и вакансию', offerai: true, chatgpt: false },
  { feature: 'Разбор ответа по конкретным критериям', offerai: true, chatgpt: 'Приблизительно' },
  { feature: 'Голосовой режим', offerai: true, chatgpt: false },
  { feature: 'Пробное интервью целиком', offerai: true, chatgpt: false },
  { feature: 'Прогресс от сессии к сессии', offerai: true, chatgpt: false },
  { feature: 'Разбор оффера и переговоры', offerai: true, chatgpt: 'Частично' },
  { feature: 'Понятно, что именно улучшить', offerai: true, chatgpt: 'Не всегда' },
  { feature: 'Готовый каркас переработанного ответа', offerai: true, chatgpt: false },
]

function CheckIcon() {
  return (
    <svg width="18" height="18" fill="none" stroke="currentColor" strokeWidth="2.5" viewBox="0 0 24 24" className="text-accent">
      <path strokeLinecap="round" strokeLinejoin="round" d="M4.5 12.75l6 6 9-13.5" />
    </svg>
  )
}

function XIcon() {
  return (
    <svg width="18" height="18" fill="none" stroke="currentColor" strokeWidth="2.5" viewBox="0 0 24 24" className="text-border">
      <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
    </svg>
  )
}

export default function Comparison() {
  return (
    <section id="sravnenie" className="py-16 sm:py-20 bg-white">
      <div className="max-w-6xl mx-auto px-4 sm:px-6">
        <div className="max-w-xl mb-10">
          <p className="text-xs font-medium text-accent uppercase tracking-widest mb-3">Сравнение</p>
          <h2 className="section-title mb-3">OfferAI vs ChatGPT</h2>
          <p className="section-subtitle">
            ChatGPT — хороший инструмент для многих задач. Но для подготовки к интервью он не знает ничего о вас и вашей вакансии.
          </p>
        </div>

        <div className="overflow-x-auto">
          <table className="w-full text-sm">
            <thead>
              <tr className="border-b border-border">
                <th className="text-left py-3 pr-6 text-text-secondary font-medium w-full">Параметр</th>
                <th className="text-center py-3 px-6 text-accent font-semibold whitespace-nowrap">OfferAI</th>
                <th className="text-center py-3 px-6 text-text-secondary font-medium whitespace-nowrap">ChatGPT</th>
              </tr>
            </thead>
            <tbody>
              {rows.map((row, idx) => (
                <tr
                  key={row.feature}
                  className={`border-b border-border ${idx % 2 === 0 ? 'bg-white' : 'bg-bg'}`}
                >
                  <td className="py-3.5 pr-6 text-text-primary">{row.feature}</td>
                  <td className="py-3.5 px-6 text-center">
                    {row.offerai === true ? <CheckIcon /> : (
                      <span className="text-xs text-text-secondary">{row.offerai}</span>
                    )}
                  </td>
                  <td className="py-3.5 px-6 text-center">
                    {row.chatgpt === false ? (
                      <XIcon />
                    ) : (
                      <span className="text-xs text-text-secondary">{row.chatgpt}</span>
                    )}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>

        <div className="mt-8 p-4 bg-accent-light rounded-xl text-sm text-accent">
          Главное отличие — OfferAI знает, кто вы и на какую позицию идёте. Это меняет качество и точность разбора.
        </div>
      </div>
    </section>
  )
}
