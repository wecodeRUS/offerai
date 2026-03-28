export default function Hero() {
  return (
    <section className="bg-bg pt-14 pb-10 sm:pt-20 sm:pb-16">
      <div className="max-w-6xl mx-auto px-4 sm:px-6">
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-14 items-center">
          {/* Left — text */}
          <div>
            <h1 className="text-3xl sm:text-4xl lg:text-[2.75rem] font-semibold text-text-primary leading-tight tracking-tight mb-4">
              Вы знаете ответ.<br />
              Но на собеседовании — теряетесь.
            </h1>

            <p className="text-base text-text-secondary leading-relaxed mb-6 max-w-md">
              Загрузите резюме и вакансию. Ответьте на вопросы. Получите конкретный разбор — что не так и как это исправить.
            </p>

            <div className="flex flex-col sm:flex-row items-start sm:items-center gap-3 mb-4">
              <a href="https://app.getofferai.ru/register" className="btn-primary px-6 py-3 text-base">
                Попробовать бесплатно
              </a>
              <a href="#demo" className="text-sm text-text-secondary hover:text-accent transition-colors">
                Посмотреть пример ↓
              </a>
            </div>

            <p className="text-xs text-text-secondary">Без карты. Без подписки.</p>
          </div>

          {/* Right — mockup */}
          <div className="bg-white rounded-2xl border border-border shadow-sm overflow-hidden">
            {/* Window bar */}
            <div className="flex items-center gap-1.5 px-4 py-2.5 border-b border-border bg-bg">
              <div className="w-2 h-2 rounded-full bg-red-300"></div>
              <div className="w-2 h-2 rounded-full bg-yellow-300"></div>
              <div className="w-2 h-2 rounded-full bg-green-300"></div>
              <span className="ml-3 text-[11px] text-text-secondary">Разбор ответа</span>
            </div>

            <div className="p-4 space-y-3">
              {/* Question */}
              <div className="flex items-start gap-2.5">
                <div className="w-6 h-6 rounded-full bg-accent-light flex items-center justify-center flex-shrink-0 mt-0.5">
                  <span className="text-accent text-[10px] font-bold">HR</span>
                </div>
                <p className="text-sm font-medium text-text-primary">Расскажите о сложном проекте, в котором вы участвовали.</p>
              </div>

              {/* Weak answer */}
              <div className="bg-bg rounded-lg px-3 py-2 text-[13px] text-text-secondary leading-relaxed">
                Мы делали большой сервис для банка. Было много сложностей. В итоге всё получилось, я многому научился.
              </div>

              {/* Score bars */}
              <div className="grid grid-cols-3 gap-2">
                {[
                  { label: 'Конкретика', score: 1 },
                  { label: 'Структура', score: 2 },
                  { label: 'Ваша роль', score: 1 },
                ].map((item) => (
                  <div key={item.label} className="text-center">
                    <div className="flex gap-0.5 justify-center mb-1">
                      {Array.from({ length: 5 }).map((_, i) => (
                        <div
                          key={i}
                          className={`w-3 h-1 rounded-full ${i < item.score ? 'bg-accent' : 'bg-border'}`}
                        />
                      ))}
                    </div>
                    <span className="text-[10px] text-text-secondary">{item.label}</span>
                  </div>
                ))}
              </div>

              {/* Suggestion */}
              <div className="border-l-2 border-accent pl-3 py-1">
                <p className="text-[12px] text-text-secondary">
                  <span className="font-medium text-accent">Что исправить:</span> добавьте название проекта, вашу роль и конкретный результат с цифрами.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  )
}
