# Шаблон Singleton

Задача: предача объекта от одного объекта к другому в любой части приложения.

Проблема: использование глобальных переменных признак плохого тона и в современных веб приложениях не допустимо.

Условия:
* В системе не должно быть испозьзованы глобальные переменные
* Должен быть только один экземпляр запрашиваемого объекта
* Экземпляр объекта должен быть доступен для любого объекта в системе

Решение:
* Создание объекта, экзепляр которого не возможно создать за пределами этого объекта, для этого конструктору объекта задается уровень доступа private
* Получение экземпляра объекта производится через статический метод, который создает экземпляр только 1 раз