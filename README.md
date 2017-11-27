Użytkownik ma możliwość logowania się do API podając email oraz hasło. Są dwa typy użytkowników: administrator (typ przypisywany ręcznie w bazie) oraz zwykły użytkownik (typ nadawany po rejestracji).

Administrator:

- dodawanie pytań wraz z plikiem graficznym, odpowiedziami oraz możliwością zaznaczenia, która odpowiedz jest poprawna (tylko jedna odpowiedź poprawna),
- administrator ma możliwość tworzenia kategorii, do których będzie przypisywał pytania,
- administrator może usuwać pytania lub ukrywać je,
- administrator może usuwać kategorie lub ukrywać je,
- administrator może zarządzać użytkownikami tj. dodawać, edytować lub usuwać (SoftDeletable).

Użytkownik:

- loguje się / wylogowuje się,
- pobiera listę aktywnych kategorii,
- pobiera losowo zwracane aktywne pytania z danej kategorii wraz z adresem do pliku graficznego oraz losowe odpowiedzi z danego pytania z zaznaczoną tą poprawną (^^),
- wysyła pytania wraz z odpowiedziami do serwera oraz otrzymuje wynik w postaci listy pytań z zaznaczonymi poprawnymi odpowiedziami.
