'use client'

import { useState } from 'react'

export default function Demo() {
  const [showAfter, setShowAfter] = useState(false)

  return (
    <section id="demo" className="py-14 sm:py-20 bg-white">
      <div className="max-w-6xl mx-auto px-4 sm:px-6">
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-10 items-start">
          {/* Left — explanation */}
          <div>
            <p className="text-xs font-medium text-accent uppercase tracking-widest mb-3">Пример</p>
            <h2 className="section-title mb-4">Было → Стало</h2>
            <p className="text-sm text-text-secondary leading-relaxed mb-6">
              Кандидат на позицию backend-разработчика отвечает на стандартный вопрос. Переключите — и увидите разницу после работы с агентом.
            </p>

            {/* Toggle */}
            <div className="flex gap-2 mb-6">
              <button
                onClick={() => setShowAfter(false)}
                className={`flex-1 py-2.5 text-sm font-medium rounded-lg border transition-colors ${
                  !showAfter ? 'bg-accent-light border-accent text-accent' : 'bg-white border-border text-text-secondary'
                }`}
              >
                До
              </button>
              <button
                onClick={() => setShowAfter(true)}
                className={`flex-1 py-2.5 text-sm font-medium rounded-lg border transition-colors ${
                  showAfter ? 'bg-accent-light border-accent text-accent' : 'bg-white border-border text-text-secondary'
                }`}
              >
                После
              </button>
            </div>

            {/* Answer text */}
            <div className="card">
              <p className="text-sm text-text-secondary leading-relaxed">
                {showAfter
                  ? 'Год назад я мигрировал процессинг платёжных уведомлений на микросервисы. Команда — 4 человека, около 50 000 транзакций в сутки. Я отвечал за слой трансформации данных и интеграционные тесты. Мигрировали за три недели без инцидентов.'
                  : 'Мы делали большой сервис для банка. Было много сложностей с производительностью. Команда работала несколько месяцев, в итоге всё получилось. Я многому научился.'}
              </p>
            </div>
          </div>

          {/* Right — scores */}
          <div className="card space-y-4">
            <p className="text-xs font-medium text-text-secondary uppercase tracking-wide">Оценка по критериям</p>

            {[
              { label: 'Конкретность', before: 1, after: 5 },
              { label: 'Структура', before: 2, after: 5 },
              { label: 'Попадание в вакансию', before: 3, after: 4 },
              { label: 'Убедительность', before: 1, after: 5 },
              { label: 'Ваша роль видна', before: 1, after: 5 },
            ].map((c) => {
              const score = showAfter ? c.after : c.before
              return (
                <div key={c.label}>
                  <div className="flex justify-between items-center mb-1">
                    <span className="text-sm text-text-primary">{c.label}</span>
                    <span className="text-xs text-text-secondary font-medium">{score}/5</span>
                  </div>
                  <div className="flex gap-1">
                    {Array.from({ length: 5 }).map((_, i) => (
                      <div
                        key={i}
                        className={`flex-1 h-1.5 rounded-full transition-colors duration-300 ${
                          i < score
                            ? showAfter ? 'bg-accent' : 'bg-highlight'
                            : 'bg-border'
                        }`}
                      />
                    ))}
                  </div>
                </div>
              )
            })}

            <div className={`rounded-lg p-3 text-xs leading-relaxed mt-2 ${showAfter ? 'bg-accent-light text-accent-dark' : 'bg-amber-50 text-amber-800'}`}>
              {showAfter
                ? 'Конкретный ответ с цифрами. Интервьюер сразу понимает, что вы делали и какой был результат.'
                : 'Нет деталей — непонятно, что за проект и что именно вы сделали. Такой ответ не запомнится.'}
            </div>

            <a href="https://app.getofferai.ru/register" className="btn-primary w-full justify-center text-sm mt-2">
              Попробовать на своём резюме
            </a>
          </div>
        </div>
      </div>
    </section>
  )
}
