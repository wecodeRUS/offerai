'use client'

import { useState } from 'react'

const beforeAnswer = `Мы делали большой сервис для банка. Было много сложностей с производительностью и интеграциями с внешними системами. Команда работала несколько месяцев, в итоге всё получилось. Я многому научился в процессе.`

const afterAnswer = `В прошлом году я участвовал в миграции процессинга платёжных уведомлений на микросервисную архитектуру. Наша команда из 4 разработчиков отвечала за сервис, через который проходило около 50 000 транзакций в сутки. Главная сложность — согласовать формат данных с тремя сторонними системами при нулевом простое. Я писал слой трансформации данных и настраивал интеграционные тесты. В итоге мигрировали без инцидентов за три недели.`

const criteria = [
  { label: 'Конкретность', before: 1, after: 5 },
  { label: 'Структура', before: 2, after: 5 },
  { label: 'Попадание в вакансию', before: 3, after: 4 },
  { label: 'Убедительность', before: 1, after: 5 },
  { label: 'Ваша роль видна', before: 1, after: 5 },
]

export default function Demo() {
  const [showAfter, setShowAfter] = useState(false)

  return (
    <section id="demo" className="py-16 sm:py-20 bg-bg">
      <div className="max-w-6xl mx-auto px-4 sm:px-6">
        {/* Section header */}
        <div className="max-w-xl mb-10">
          <p className="text-xs font-medium text-accent uppercase tracking-widest mb-3">Пример разбора</p>
          <h2 className="section-title mb-3">Как выглядит работа с агентом</h2>
          <p className="section-subtitle">
            Backend-разработчик готовится к собеседованию в финтех-компанию. Вопрос про сложный проект — один из самых частых.
          </p>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
          {/* Left: conversation */}
          <div className="space-y-4">
            {/* Question */}
            <div className="card">
              <div className="flex items-center gap-2 mb-3">
                <div className="w-7 h-7 rounded-full bg-accent-light flex items-center justify-center">
                  <span className="text-accent text-xs font-semibold">HR</span>
                </div>
                <span className="text-xs text-text-secondary">Вопрос интервьюера</span>
              </div>
              <p className="text-sm text-text-primary font-medium">
                Расскажите о самом сложном техническом проекте, в котором вы участвовали.
              </p>
            </div>

            {/* Toggle */}
            <div className="flex gap-2">
              <button
                onClick={() => setShowAfter(false)}
                className={`flex-1 py-2 text-sm font-medium rounded-lg border transition-colors ${
                  !showAfter
                    ? 'bg-white border-accent text-accent'
                    : 'bg-white border-border text-text-secondary hover:border-text-secondary'
                }`}
              >
                До работы с агентом
              </button>
              <button
                onClick={() => setShowAfter(true)}
                className={`flex-1 py-2 text-sm font-medium rounded-lg border transition-colors ${
                  showAfter
                    ? 'bg-white border-accent text-accent'
                    : 'bg-white border-border text-text-secondary hover:border-text-secondary'
                }`}
              >
                После
              </button>
            </div>

            {/* Answer */}
            <div className="card">
              <div className="flex items-center gap-2 mb-3">
                <div className="w-7 h-7 rounded-full bg-bg border border-border flex items-center justify-center">
                  <span className="text-text-secondary text-xs font-semibold">Я</span>
                </div>
                <span className="text-xs text-text-secondary">Ответ кандидата</span>
              </div>
              <p className="text-sm text-text-secondary leading-relaxed">
                {showAfter ? afterAnswer : beforeAnswer}
              </p>
            </div>
          </div>

          {/* Right: score */}
          <div className="card space-y-5">
            <div>
              <p className="text-xs font-medium text-text-secondary uppercase tracking-wide mb-1">Оценка по критериям</p>
              <p className="text-xs text-text-secondary">
                {showAfter ? 'После доработки с агентом' : 'Первый вариант ответа'}
              </p>
            </div>

            <div className="space-y-3">
              {criteria.map((c) => {
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
                              ? showAfter
                                ? 'bg-accent'
                                : 'bg-highlight'
                              : 'bg-border'
                          }`}
                        />
                      ))}
                    </div>
                  </div>
                )
              })}
            </div>

            {/* Comment */}
            <div className={`rounded-lg p-3 text-xs leading-relaxed ${showAfter ? 'bg-accent-light text-accent' : 'bg-amber-50 text-amber-800'}`}>
              {showAfter ? (
                <span>Ответ стал конкретным и структурированным. Интервьюер сразу понимает вашу роль и результат.</span>
              ) : (
                <span>Нет конкретики: непонятно, что за сервис, что именно вы делали и каков был результат. Это типичный ответ, который не запоминается.</span>
              )}
            </div>

            <a
              href="https://app.getofferai.ru/register"
              className="btn-primary w-full justify-center text-sm"
            >
              Попробовать на своём резюме
            </a>
          </div>
        </div>
      </div>
    </section>
  )
}
