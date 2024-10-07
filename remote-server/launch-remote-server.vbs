Set WshShell = CreateObject("WScript.Shell")

Do While True
  ' Закрыть предыдущий процесс
  WshShell.Run "taskkill /im node.exe /f", 0, True
  
  WScript.Sleep(10000)
  ' Запустить новый процесс
  WshShell.Run "cmd.exe /c E:\Emil\xampp\htdocs\remote-server\start-remote-server.bat", 1, False
  
  ' Подождать 15 минут перед следующим запуском
  WScript.Sleep(10000) ' 15 минут
Loop